<?php
require_once("wrapperdbbase.php");
require_once("common.php");

interface IWrapperDBComments
{
    public function getCommentsByMarketID($marketID, $start, $count, $token, $userID);
    public function postComment($marketID, $userID, $mark, $text, $token);
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

    public function getCommentsByMarketID($marketID, $start, $count, $token, $userID)
    {
        $marketID = $this->connection->real_escape_string($marketID);
        $start = $this->connection->real_escape_string($start);
        $count = $this->connection->real_escape_string($count);

        $query = "SELECT * FROM comments WHERE marketID='".$marketID."' LIMIT ".$start.", ".$count."";
        $result = $this->connection->query($query);
        if($this->connection->errno)
            return ERRORS::GET_COMMENTS_MYSQL_ERROR;

        if($result->num_rows)
        {
            $userRights = 0;
            if($this->checkToken($userID, $token) == ERRORS::NO_ERROR)
            {
                $userRights = $this->getUserRights($userID);
            }
            $comments = array();
            for($i = 0; $i < $result->num_rows; $i++)
            {
                $row = $result->fetch_assoc();
                $comment = NULL;
                if(($userRights == 1) || ($userRights == 2))
                {
                    $comment = new CommentAdmin($row['commentID'], $row['marketID'], $row['userID'],
                        $row['commentTime'], utf8_decode($row['text']), $row['mark'],
                        $row['photos'], $row['approved']);
                }
                else
                {
                    $comment = new Comment($row['commentID'], $row['marketID'], $row['userID'],
                        $row['commentTime'], utf8_decode($row['text']), $row['mark'],
                        $row['photos']);
                }
                $comments[$i] = $comment;
            }
            print_r($comments);
            $this->setData($comments);
            return ERRORS::NO_ERROR;
        }
        return ERRORS::NO_ERROR;
    }

    public function postComment($marketID, $userID, $mark, $text, $token)
    {
        $res = $this->checkToken($userID, $token);
        $marketID = $this->connection->real_escape_string($marketID);
        $userID = $this->connection->real_escape_string($userID);
        $mark = $this->connection->real_escape_string($mark);
        $text = $this->connection->real_escape_string($text);
        if($res == ERRORS::NO_ERROR)
        {
            $ts = time();
            if(($mark > 0) && ($mark <= ServerSettings::getMaxMarketMark()))
            {
                if(strlen(($text) >= ServerSettings::getMinCommentLength()) && (strlen($text) <= ServerSettings::getMaxCommentLength()))
                {
                    $query = "INSERT INTO comments (commentID, marketID, userID, commentTime, text, mark, photos, approved, thumbsUp, thumbsDown) ".
                        "VALUES (NULL, '".$marketID."', '".$userID."', FROM_UNIXTIME('".$ts."'), '".$text."', '".$mark."', NULL, '0', '0', '0')";
                    $result = $this->connection->query($query);
                    echo $query;
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
};
?>