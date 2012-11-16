<?php

require_once("wrapperdbbase.php");

interface IWrapperDBComments
{
    public function getCommentsByMarket($marketID, $start, $count, $token, $userID);
    public function getCommentsByUser($userID, $start, $count, $token);
    public function getCommentByID($commentID, $token, $userID);
    public function postComment($marketID, $userID, $mark, $text, $token);
    public function rankComment($commentID, $isThumbsUp, $token, $userID);
    public function editComment($commentID, $text,$token, $userID);
    public function deleteComment($commentID, $token, $userID);
    public function approveComment($commentID, $token, $userID);
}

class WrapperDBComments extends WrapperDBBase implements IWrapperDBComments
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    private function fetchCommentsFromRequest($result, $userID, $token)
    {
        if($result->num_rows)
        {
            if($this->checkToken($userID, $token) == ERRORS::NO_ERROR)
            {
                $isModerator = $this->isUserModerator($userID);
                $comments = array();
                for($i = 0; $i < $result->num_rows; $i++)
                {
                    $row = $result->fetch_assoc();
                    $comment = NULL;
                    if($isModerator)
                    {
                        $comment = new CommentModer($row['commentID'], $row['marketID'], $row['userID'],
                            $row['commentTime'], $row['text'], $row['mark'],
                            $row['photos'], $row['approved']);
                    }
                    else
                    {
                        $comment = new Comment($row['commentID'], $row['marketID'], $row['userID'],
                            $row['commentTime'], $row['text'], $row['mark'],
                            $row['photos']);
                    }
                    $comments[$i] = $comment;

                }
                $this->setData($comments);
            }
        }
        return ERRORS::NO_ERROR;
    }

    public function getCommentsByMarket($marketID, $start, $count, $token, $userID)
    {
        $marketID = $this->connection->real_escape_string($marketID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        if($count > ServerSettings::maxCommentsInRequest)
            $count = ServerSettings::maxCommentsInRequest;

        $query = "SELECT * FROM comments WHERE marketID='".$marketID."' LIMIT ".$start.", ".$count;
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        return fetchCommentsFromRequest($result, $userID, $token);
    }

    public function getCommentsByUser($userID, $start, $count, $token)
    {
        $userID = $this->connection->real_escape_string($userID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        if($count > ServerSettings::maxCommentsInRequest)
            $count = ServerSettings::maxCommentsInRequest;

        $query = "SELECT * FROM comments WHERE userID='".$userID."' LIMIT ".$start.", ".$count;
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        return fetchCommentsFromRequest($result, $userID, $token);
    }

    public function getCommentByID($commentID, $token, $userID)
    {
        $commentID = $this->connection->real_escape_string($commentID);
        $start = $this->connection->real_escape_string($token);
        $count = $this->connection->real_escape_string($userID);

        $query = "SELECT * FROM comments WHERE $commentID='".$commentID."'";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        return fetchCommentsFromRequest($result, $userID, $token);
    }

    public function postComment($marketID, $userID, $mark, $text, $token)
    {
        $marketID = $this->connection->real_escape_string($marketID);
        $userID = $this->connection->real_escape_string($userID);
        $mark = $this->connection->real_escape_string($mark);
        $text = $this->connection->real_escape_string($text);

        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $ts = time();
            if(Validator::validateMark($mark))
            {
                if(Validator::validateCommentLength($text))
                {
                    // TODO: Photos
                    $query = "INSERT INTO comments (commentID, marketID, userID, commentTime, text, mark, photos, approved, thumbsUp, thumbsDown) ".
                        "VALUES (NULL, '".$marketID."', '".$userID."', FROM_UNIXTIME('".$ts."'), '".$text."', '".$mark."', NULL, '0', '0', '0')";
                    $result = $this->connection->query($query);
                    if($this->connection->errno)
                        return ERRORS::POST_COMMENT_MYSQL_ERROR;
                    return ERRORS::NO_ERROR;
                }
                else
                {
                    return ERRORS::BAD_COMMENT_LENGTH;
                }
            }
            else
            {
                return ERRORS::BAD_MARKET_MARK;
            }
        }
        else
        {
            return $res;
        }
    }

    public function rankComment($commentID, $isThumbsUp, $token, $userID)
    {
        $commentID = $this->connection->real_escape_string($commentID);
        $res = $this->checkToken($userID, $token);

        if($res == ERRORS::NO_ERROR)
        {
            $query = "";
            if($isThumbsUp)
            {
                $query = "UPDATE comments SET thumbsUp = thumbsUp + 1 WHERE commentID='".$commentID."'";
            }
            else
            {
                $query = "UPDATE comments SET thumbsDown = thumbsDown + 1 WHERE commentID='".$commentID."'";
            }
            $result = $this->connection->query($query);
            if($this->connection->errno)
                return ERRORS::RANK_COMMENT_MYSQL_ERROR;
            return ERRORS::NO_ERROR;
        }
        else
        {
            return $res;
        }
    }

    public function editComment($commentID, $text, $token, $userID)
    {
        // TODO: Author sholub be able to edit comment
        $commentID = $this->connection->real_escape_string($commentID);
        $userID = $this->connection->real_escape_string($userID);
        $text = $this->connection->real_escape_string($text);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $userRights = $this->getUserRights($userID);
            if(Validator::isAdmin($userRights) || Validator::isModerator($userRights))
            {
                if(Validator::validateCommentLength($text))
                {
                    $query = "UPDATE comments SET text='".$text."' WHERE commentID='".$commentID."'";
                    $result = $this->connection->query($query);
                    if($this->connection->errno)
                        return ERRORS::DELETE_COMMENT_MYSQL_ERROR;
                    return ERRORS::NO_ERROR;
                }
                else
                {
                    return ERRORS::BAD_COMMENT_LENGTH;
                }
            }
            else
            {
                return ERRORS::PERMISSION_DENIED;
            }
        }
        else
        {
            return $res;
        }
    }

    public function deleteComment($commentID, $token, $userID)
    {
        // TODO: Author sholub be able to delete comment
        $commentID = $this->connection->real_escape_string($commentID);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $userRights = $this->getUserRights($userID);
            if(Validator::isAdmin($userRights) || Validator::isModerator($userRights))
            {
                $query = "DELETE FROM comments WHERE commentID='".$commentID."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                    return ERRORS::DELETE_COMMENT_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
            else
            {
                return ERRORS::PERMISSION_DENIED;
            }
        }
        else
        {
            return $res;
        }
    }

    public function approveComment($commentID, $token, $userID)
    {

    }
}

class Comment
{
    public $commentID;
    public $marketID;
    public $userID;
    public $commentTime;
    public $text;
    public $mark;
    public $photos;
    public $ranked;

    function __construct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                         $_mark, $_photos)
    {
        $this->commentID =   $_commentID;
        $this->marketID =    $_marketID;
        $this->userID =      $_userID;
        $this->commentTime = $_commentTime;
        $this->text =        $_text;
        $this->mark =        $_mark;
        $this->photos =      $_photos;
        $this->ranked =      NULL;
    }
}

class CommentModer extends Comment
{
    public $approved;
    function __construct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                     $_mark, $_photos, $_approved)
    {
        parent::__contruct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                           $_mark, $_photos);
        $this->approved =    $_approved;
    }
}

?>