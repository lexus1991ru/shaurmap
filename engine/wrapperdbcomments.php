<?php

require_once("wrapperdbbase.php");

interface IWrapperDBComments
{
    public function getCommentsByMarket($marketID, $start, $count, $token, $userID);
    public function getCommentsByUser($userID, $start, $count, $token, $targetUserID);
    public function getCommentByID($commentID, $token, $userID);
    public function canPostComment($token, $userID);
    public function postComment($marketID, $userID, $mark, $text, $token);
    public function rankComment($commentID, $isThumbsUp, $token, $userID);
    public function editComment($commentID, $text,$token, $userID);
    public function deleteComment($commentID, $token, $userID);
    public function getUnmoderatedComments($start, $count, $token, $userID);
    public function approveComment($commentID, $action, $token, $userID);
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

    // GOVNOKOD #1
    // If $out is true result will be send to ajax
    // If $out is false it just return true or false(internal use)
    public function canPostComment($token, $userID, $out = true)
    {
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $comments = $this->commentsOnDay($userID, $token);
            if(!$out)
            {
                return ($comments <= ServerSettings::maxCommentsPerDay) ? true : false;
            }
            if($comments <= ServerSettings::maxCommentsPerDay)
            {
                $this->setData(1);
            }
            else
            {
                $this->setData(0);
            }
            return ERRORS::NO_ERROR;
        }
        if(!$out)
        {
            return false;
        }
        return $res;
    }

    private function canEditComment($commentID, $userID)
    {

        if($this->isUserModerator($userID))
            return true;
        $query = "SELECT commentID FROM comments WHERE userID='".$userID."' AND commentID='".$commentID."'";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return false;
        if($result->num_rows)
            return true;
        return false;
    }

    private function commentsLastDay($userID, $token)
    {
        $count = 0;
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $ts = time();
            $day = 86400; //60*60*24 = day
            $query = "SELECT COUNT(commentID) AS comments_count FROM comments WHERE userID='".$userID."' AND UNIX_TIMESTAMP(commentTime) > '".$ts - $day."'";
            $result = $this->connection->query($query);
            if($this->connection->errno)
                return ERRORS::COMMENTS_DAY_MYSQL_ERROR;
            if($result->num_rows)
            {
                $row = $result->fetch_assoc();
                $count = $row['comments_count'];
            }
        }
        return $count;
    }

    private function fetchCommentsFromRequest($result, $userID)
    {
        if($result->num_rows)
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
                        $row['photos'], $row['thumbType'], $row['approved']);
                }
                else
                {
                    $comment = new Comment($row['commentID'], $row['marketID'], $row['userID'],
                        $row['commentTime'], $row['text'], $row['mark'],
                        $row['photos'], $row['thumbType']);
                }
                $comments[$i] = $comment;
            }
            $this->setData($comments);
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

        $query = "";
        if($this->checkToken($userID, $token) == ERRORS::NO_ERROR)
            $query = "SELECT * FROM comments as t1 LEFT JOIN (SELECT thumbType, commentID AS cID FROM thumbs WHERE userID='".$userID."') as t2".
                     " ON t1.commentID=t2.cID WHERE marketID='".$marketID."' LIMIT ".$start.", ".$count;
        else
            $query = "SELECT * FROM comments WHERE marketID='".$marketID."' LIMIT ".$start.", ".$count;
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;
        return $this->fetchCommentsFromRequest($result, $userID);
    }

    public function getCommentsByUser($userID, $start, $count, $token, $targetUserID)
    {
        $userID = $this->connection->real_escape_string($userID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);
        $targetUserID = $this->connection->real_escape_string($targetUserID);

        if($count > ServerSettings::maxCommentsInRequest)
            $count = ServerSettings::maxCommentsInRequest;

        $query = "";
        if($this->checkToken($userID, $token) == ERRORS::NO_ERROR)
            $query = "SELECT * FROM comments as t1 LEFT JOIN (SELECT thumbType, commentID FROM thumbs WHERE userID='".$userID."') as t2".
                     " ON t1.commentID=t2.commentID WHERE userID='".$targetUserID."' LIMIT ".$start.", ".$count;
        else
            $query = "SELECT * FROM comments WHERE userID='".$targetUserID."' LIMIT ".$start.", ".$count;
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        return $this->fetchCommentsFromRequest($result, $userID);
    }

    public function getCommentByID($commentID, $token, $userID)
    {
        $commentID = $this->connection->real_escape_string($commentID);
        $start = $this->connection->real_escape_string($token);
        $count = $this->connection->real_escape_string($userID);

        $query = "";
        if($this->checkToken($userID, $token) == ERRORS::NO_ERROR)
            $query = "SELECT * FROM comments as t1 LEFT JOIN (SELECT thumbType, commentID FROM thumbs WHERE userID='".$userID."') as t2".
                     " ON t1.commentID=t2.commentID WHERE t1.commentID='".$commentID."'";
        else
            $query = "SELECT * FROM comments WHERE commentID='".$commentID."'";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        return $this->fetchCommentsFromRequest($result, $userID);
    }

    public function rankComment($commentID, $isThumbsUp, $token, $userID)
    {

        // SELECT * FROM comments as t1 LEFT JOIN (SELECT thumbType, commentID FROM thumbs WHERE userID='9') as t2 ON t1.commentID=t2.commentID WHERE marketID='5'
        $commentID = $this->connection->real_escape_string($commentID);
        $userID = $this->connection->real_escape_string($userID);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            $query = "SELECT thumbType, commentID FROM thumbs WHERE userID='".$userID."' AND commentID='".$commentID."'";
            $result = $this->connection->query($query);
            if($this->connection->errno)
                return ERRORS::RANK_COMMENT_MYSQL_ERROR;
            if($result->num_rows)
                return ERRORS::COMMENT_ALREADY_RANKED;
            $query1 = "";
            if($isThumbsUp)
            {
                $query =  "UPDATE comments SET thumbsUp = thumbsUp + 1 WHERE commentID='".$commentID."'";
                $query1 = "INSERT INTO thumbs (thumbID, userID, commentID, thumbType, thumbsTime) ".
                         "VALUES(NULL, '".$userID."', '".$commentID."', '1', FROM_UNIXTIME('".time()."'))";
            }
            else
            {
                $query =  "UPDATE comments SET thumbsDown = thumbsDown + 1 WHERE commentID='".$commentID."'";
                $query1 = "INSERT INTO thumbs (thumbID, userID, commentID, thumbType, thumbsTime) ".
                         "VALUES(NULL, '".$userID."', '".$commentID."', '0', FROM_UNIXTIME('".time()."'))";
            }
            $result = $this->connection->query($query);
            if($this->connection->errno)
                return ERRORS::RANK_COMMENT_MYSQL_ERROR;
            $result = $this->connection->query($query1);
            if($this->connection->errno)
                return ERRORS::RANK_COMMENT_MYSQL_ERROR;
            return ERRORS::NO_ERROR;
        }
        return $res;
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
            $day = 86400; //60*60*24 = day
            if(Validator::validateMark($mark))
            {
                if(Validator::validateCommentLength($text))
                {
                    if($this->canPostComment($token, $userID, false))
                    {
                        // TODO: Photos
                        $query = "INSERT INTO comments (commentID, marketID, userID, commentTime, text, mark, photos, approved, thumbsUp, thumbsDown) ".
                            "VALUES (NULL, '".$marketID."', '".$userID."', FROM_UNIXTIME('".$ts."'), '".$text."', '".$mark."', NULL, NULL, '0', '0')";
                        $result = $this->connection->query($query);
                        if($this->connection->errno)
                            return ERRORS::POST_COMMENT_MYSQL_ERROR;
                        return ERRORS::NO_ERROR;
                    }
                    else
                    {
                        return ERRORS::COMMENT_LIMIT_REACHED;
                    }
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
        return $res;
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
            if(Validator::validateCommentLength($text))
            {
                if($this->canEditComment($commentID, $userID))
                {
                    // TODO: Photos
                    $query = "UPDATE comments SET text='".$text."' WHERE commentID='".$commentID."'";
                    $result = $this->connection->query($query);
                    if($this->connection->errno)
                        return ERRORS::EDIT_COMMENT_MYSQL_ERROR;
                    return ERRORS::NO_ERROR;
                }
                else
                {
                    return ERRORS::PERMISSION_DENIED;
                }
            }
            else
            {
                return ERRORS::BAD_COMMENT_LENGTH;
            }
        }
        return $res;
    }

    public function deleteComment($commentID, $token, $userID)
    {
        $commentID = $this->connection->real_escape_string($commentID);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            if($this->canEditComment($commentID, $userID))
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
        return $res;
    }

    public function getUnmoderatedComments($start, $count, $token, $userID)
    {
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);
        $userID = $this->connection->real_escape_string($userID);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            if($this->isUserModerator($userID))
            {
                $query = "SELECT * FROM comments WHERE ISNULL(approved) LIMIT ".$start.", ".$count;
                $result = $this->connection->query($query);
                if($this->connection->errno)
                    return ERRORS::GET_COMMENTS_MYSQL_ERROR;
                return $this->fetchCommentsFromRequest($result, $userID);
            }
            else
            {
                return ERRORS::PERMISSION_DENIED;
            }
        }
        return $res;
    }

    public function approveComment($commentID, $action, $token, $userID)
    {
        // $action = 0 - NOT approved
        // $action = 1 - approved
        $commentID = $this->connection->real_escape_string($commentID);
        $action = $this->connection->real_escape_string($action);
        $userID = $this->connection->real_escape_string($userID);
        $res = $this->checkToken($userID, $token);
        if($res == ERRORS::NO_ERROR)
        {
            if($this->isUserModerator($userID))
            {
                $query = "";
                if($action)
                    $query = "UPDATE comments SET approved='1' WHERE commentID='".$commentID."'";
                else
                    $query = "UPDATE comments SET approved='0' WHERE commentID='".$commentID."'";
                $result = $this->connection->query($query);
                if($this->connection->errno)
                    return ERRORS::APPROVE_COMMENT_MYSQL_ERROR;
                return ERRORS::NO_ERROR;
            }
            else
            {
                return ERRORS::PERMISSION_DENIED;
            }
        }
        return $res;
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
    public $thumbType;

    function __construct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                         $_mark, $_photos, $_ranked)
    {
        $this->commentID =   $_commentID;
        $this->marketID =    $_marketID;
        $this->userID =      $_userID;
        $this->commentTime = $_commentTime;
        $this->text =        $_text;
        $this->mark =        $_mark;
        $this->photos =      $_photos;
        $this->thumbType =   NULL;
    }
}

class CommentModer extends Comment
{
    public $approved;
    function __construct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                     $_mark, $_photos, $_thumbType, $_approved)
    {
        parent::__construct($_commentID, $_marketID, $_userID, $_commentTime, $_text,
                           $_mark, $_photos, $_thumbType);
        $this->approved =    $_approved;
    }
}

?>