<?php
class Topic_Model extends Base_Model {
    
    var $title = '';
    var $slug = '';
    var $body = '';
	var $bodyRaw = '';
    var $date_added = 0;
    var $date_submit = 0;
    var $charid = '';
    var $user_id = 0;
    var $user = '';
	var $username = '';
	var $avatar = '';
    var $user_board_id = 0;
    var $url = '';
    var $comment_first_id = 0;
    var $comment_last_id = 0;
    var $comment_first = '';
    var $comment_last = '';
    var $comment_first_user = '';
	var $comment_first_username = '';
    var $comment_last_user = '';
	var $comment_last_username = '';
	var $comment_first_avatar = '';
    var $comment_last_avatar = '';
    var $comments = 0;
    var $board_id = 0;
    var $board = '';
    var $urls = '';
	var $hidePublic = 0;
	var $isPrivate = 0;
	var $type = '';
	var $like1 = 0;
	var $like2 = 0;
	var $like3 = 0;
	var $locked = 0;
	var $rating_game_id = 0;
	var $rating = 0;
	var $cluster_topic_id = 0;
	var $isCluster = 0;
	var $tags = '';
    
    var $date_added_format = 0;
    var $date_submit_format = 0;
	var $date_added_iso = 0;
    var $date_submit_iso = 0;
    var $url_embed = '';
    var $boards = array();
    var $url_arr = array();
    var $url_embeds = array();
    var $urlsdisplay = '';
	var $urlsdisplaymini = '';
	var $boardslug = '';
	var $boardtype = '';
	var $urltypes = '';
	var $urldomain = '';
	var $urldomainlink = '';
	var $titlebody = '';
	
    function __construct()
    {
	$this->dbTable = "topics";
        parent::__construct();
    }

    function format($censor=0)
	{
		$strippedbody = str_replace('<img', '[img]<img', $this->body);
		$strippedbody = $this->filter(preg_replace('#(<.*?>)#is', '', $strippedbody), $censor);
        $this->title = $this->filter(html::specialchars($this->title), $censor);
		$titlespaceleft = 75 - strlen($this->title);
		$this->titlebody = $titlespaceleft > 5 ? (strlen($strippedbody) > ($titlespaceleft)) ? substr($strippedbody, 0, $titlespaceleft).'...' : $strippedbody : '';
		$this->titlebody = rtrim((strlen($strippedbody) > (256)) ? substr($strippedbody, 0, 255).'...' : $strippedbody);

		if($this->isPrivate)
			$this->title = '<img src="http://digibutter.nerr.biz/content/images/pmicon.gif"> '.$this->title;
		if($this->rating > 0)
		{
			if($this->rating == 2)
				$this->title = 'RATED: '.$this->title;
			if($this->rating == 1)
				$this->title = 'RATED: '.$this->title;
			if($this->rating == 3)
				$this->title = 'RATED: '.$this->title;
		}
		if(strlen($this->title) == 0)
			$title = $this->board;
		/*$this->body = preg_replace('#(<.*?>)#is', '', $this->body);*/
        //$this->body = $this->htmlify($this->filter(html::specialchars($this->body)));
		$this->body = $this->filter($this->body, $censor);
		$this->bodyRaw = '';
		/*
        $this->comment_first = $this->filter(preg_replace('#(<.*?>)#is', '', $this->comment_first));//$this->htmlify($this->filter(html::specialchars($this->comment_first)));
        $this->comment_last = $this->filter(preg_replace('#(<.*?>)#is', '', $this->comment_last));//$this->htmlify($this->filter(html::specialchars($this->comment_last)));
		$this->comment_first = strlen($this->comment_first) > 100 ? substr($this->comment_first, 0, 100).' ...' : $this->comment_first;
		$this->comment_last = strlen($this->comment_last) > 100 ? substr($this->comment_last, 0, 100).' ...' : $this->comment_last;
		 */
		if(!stristr($this->comment_first, '<p>'))
			$this->comment_first = $this->htmlify($this->filter($this->comment_first, $censor));
		else
			$this->comment_first = $this->filter($this->comment_first, $censor);
		if(!stristr($this->comment_last, '<p>'))
			$this->comment_last = $this->htmlify($this->filter($this->comment_last, $censor));
		else
			$this->comment_last = $this->filter($this->comment_last, $censor);
		$this->boardslug = url::title($this->board, '-');
        $this->board = $this->filter(html::specialchars($this->board), $censor);

		if(strlen($this->slug) == 0)
		{
			$this->slug = url::title($this->title, '-');
			if(strlen($this->slug) > 80)
				$this->slug = substr($this->slug, 0, 80);
		}

        $urls = explode(',', $this->urls);
        $url_arr = array();
        $url_embeds = array();
		$url_embeds_mini = array();
		$url_embeds_image = array();
		$this->urltypes = $this->urlTypes($this->urls);
		$this->urldomain = '';
		$this->urldomainlink = '';
        foreach($urls as $url)
        {
            if($this->isUrl($url))
            {
                $url_arr[] = html::specialchars($url);				
                $url_embeds[] = $this->encodeFun($url);
				$url_embeds_mini[] = $this->encodeMini($url);
				$url_embeds_image[] = $this->encodeImage($url);
				if($this->urldomain == '')
				{
					if(preg_match('#\.(jpg|jpeg|gif|png)#is', $url) || preg_match('#(deviantart.com)#is', $url))
						continue;
					if(preg_match('#(.mp3|esnips.com|thesixtyone.com)#is', $url))
						continue;
					if(preg_match('#(youtube.com|viddler.com|dailymotion.com|video.google.com|livevideo.com|gametrailers.com|metacafe.com|vimeo.com|ign.com/dor|/17-|wegame.com|megavideo.com|embedr.com|revver.com|livestream.com|blip.tv/play|g4tv.com/lvl3|collegehumor.com/video|dorkly.com/video|flipnote.hatena.com|ucbcomedy.com/videos/embed|motionbox.com)#is', $url))
						continue;
					if(preg_match('#http://(.*?)([/|,]|$).*#is', $url, $matches))
					{
						$this->urldomain = str_replace('www.','',$matches[1]);
						$this->urldomainlink = $url;
					}
				}
            }
        }
        $urlsdisplay = '';
        foreach($url_embeds as $url)
        {
            $urlsdisplay .= $url;
        }
        $this->url_arr = $url_arr;
        $this->url_embeds = $url_embeds;
        $this->urlsdisplay = $urlsdisplay;

		$urlsdisplaymini = '';
        foreach($url_embeds_mini as $url)
        {
            $urlsdisplaymini .= $url;
			if(strlen($urlsdisplaymini))
				break;
        }
        $this->urlsdisplaymini = $urlsdisplaymini;

		$titleimage = '';
        foreach($url_embeds_image as $url)
        {
            $titleimage .= $url;
			if(strlen($titleimage))
				break;
        }
        $this->titleimage = $titleimage;


        $this->date_added_format = gmdate('M d Y H:i T', $this->date_added);//$this->formatTime($this->date_added);
        $this->date_submit_format = gmdate('r', $this->date_submit);//$this->formatTime($this->date_submit);
		$this->date_added_iso = gmdate("c", $this->date_added);//<abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>;
		$this->date_submit_iso = gmdate("c", $this->date_submit);//<abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>
	}

	function formatView($censor=0)
	{
        $this->title = $this->filter(html::specialchars($this->title), $censor);
		if($this->isPrivate)
			$this->title = '[PM] '.$this->title;
                if($this->rating > 0)
                {
                        if($this->rating == 2)
                                $this->title = 'REVIEW: [MEH] '.$this->title;
                        if($this->rating == 1)
                                $this->title = 'REVIEW: [GOOD] '.$this->title;
                        if($this->rating == 3)
                                $this->title = 'REVIEW: [GREAT!] '.$this->title;
                }

        //$this->body = $this->htmlify($this->filter(html::specialchars($this->body)));
		$this->body = $this->filter($this->body, $censor);
		$this->bodyRaw = '';
        $this->comment_first = $this->htmlify($this->filter(html::specialchars($this->comment_first, $censor)));
        $this->comment_last = $this->htmlify($this->filter(html::specialchars($this->comment_last, $censor)));
		$this->boardslug = url::title($this->board, '-');
        $this->board = $this->filter(html::specialchars($this->board), $censor);

		if(strlen($this->slug) == 0)
		{
			$this->slug = url::title($this->title, '-');
			if(strlen($this->slug) > 80)
				$this->slug = substr($this->slug, 0, 80);
		}

        $urls = explode(',', $this->urls);
        $url_arr = array();
        $url_embeds = array();
        foreach($urls as $url)
        {
            if($this->isUrl($url))
            {
                $url_arr[] = html::specialchars($url);
                $url_embeds[] = $this->encodeFun($url);
            }
        }
        $urlsdisplay = '';
        foreach($url_embeds as $url)
        {
            $urlsdisplay .= $url;
        }
        $this->url_arr = $url_arr;
        $this->url_embeds = $url_embeds;
        $this->urlsdisplay = $urlsdisplay;

        //$this->date_added_format = $this->formatTime($this->date_added);//gmdate("Y-m-d g:i", $this->date_added);
        //$this->date_submit_format = $this->formatTime($this->date_submit);

		$this->date_added_format = gmdate('M d Y H:i T', $this->date_added);//$this->formatTime($this->date_added);
        $this->date_submit_format = gmdate('r', $this->date_submit);//$this->formatTime($this->date_submit);
		$this->date_added_iso = gmdate("c", $this->date_added);//<abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>;
		$this->date_submit_iso = gmdate("c", $this->date_submit);//<abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>
	}

    // Decimal > Custom
    static function dec2any( $num, $base=62, $index=false ) {
        if (! $base ) {
            $base = strlen( $index );
        } else if (! $index ) {
            $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" ,0 ,$base );
        }
        $out = "";
        for ( $t = floor( log10( $num ) / log10( $base ) ); $t >= 0; $t-- ) {
            $a = floor( $num / pow( $base, $t ) );
            $out = $out . substr( $index, $a, 1 );
            $num = $num - ( $a * pow( $base, $t ) );
        }
        return $out;
    }
    //Parameters:
    //$num - your decimal integer
    //$base - base to which you wish to convert $num (leave it 0 if you are providing $index or omit if you're using default (62))
    //$index - if you wish to use the default list of digits (0-1a-zA-Z), omit this option, otherwise provide a string (ex.: "zyxwvu")
    // Custom > Decimal
    function any2dec( $num, $base=62, $index=false ) {
        if (! $base ) {
            $base = strlen( $index );
        } else if (! $index ) {
            $index = substr( "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", 0, $base );
        }
        $out = 0;
        $len = strlen( $num ) - 1;
        for ( $t = 0; $t <= $len; $t++ ) {
            $out = $out + strpos( $index, substr( $num, $t, 1 ) ) * pow( $base, $len - $t );
        }
        return $out;
    }
    
	function getValues()
	{
		$record = $this->getValuesForSave();
        $record["user"] = $this->user;
		$record["username"] = $this->username;
		$record["avatar"] = $this->avatar;
        $record["user_board_id"] = $this->user_board_id;
        $record["comment_first"] = $this->comment_first;
        $record["comment_last"] = $this->comment_last;
        $record["comment_first_user"] = $this->comment_first_user;
        $record["comment_last_user"] = $this->comment_last_user;
		$record["comment_first_username"] = $this->comment_first_username;
        $record["comment_last_username"] = $this->comment_last_username;
		$record["comment_first_avatar"] = $this->comment_first_avatar;
        $record["comment_last_avatar"] = $this->comment_last_avatar;
        $record["board"] = $this->board;
		$record['boardtype'] = $this->boardtype;
		return $record;
	}

    function getValuesForSave()
    {
        $record = array();
		$record["id"] = $this->id;
		$record["title"] = $this->title;
		$record["slug"] = $this->slug;
		$record["body"] = $this->body;
		$record["bodyRaw"] = $this->bodyRaw;
		$record['url'] = $this->url;
        $record['charid'] = $this->charid;
        $record["user_id"] = $this->user_id;
        $record["comments"] = $this->comments;
        $record["comment_first_id"] = $this->comment_first_id;
        $record["comment_last_id"] = $this->comment_last_id;
        $record["board_id"] = $this->board_id;
		$record["hidePublic"] = $this->hidePublic;
		$record['isPrivate'] = $this->isPrivate;
        $record['urls'] = $this->urls;
		$record['type'] = $this->type;
		$record['like1'] = $this->like1;
		$record['like2'] = $this->like2;
		$record['like3'] = $this->like3;
		$record['locked'] = $this->locked;
		$record['rating'] = $this->rating;
		$record['rating_game_id'] = $this->rating_game_id;
		$record['cluster_topic_id'] = $this->cluster_topic_id;
		$record['isCluster'] = $this->isCluster;
		$record['tags'] = $this->tags;		

		//set if not set
		if($this->date_added == 0)
			$this->date_added = time();
		if($this->date_submit == 0)
			$this->date_submit = time();

		$record['date_added'] = $this->date_added;
        $record['date_submit'] = $this->date_submit;

		return $record;
    }

	function setValues($record)
	{
		$this->id = $record->id;//$record["id"];
		$this->title = $record->title;//$record["name"];
		$this->slug = $record->slug;
		$this->body = $record->body;
		$this->bodyRaw = $record->bodyRaw;
		$this->date_added = $record->date_added;
        $this->date_submit = $record->date_submit;
		$this->url = $record->url;
        $this->charid = $record->charid;
        $this->user_id = $record->user_id;
        $this->board_id = $record->board_id;
		$this->hidePublic = $record->hidePublic;
		$this->isPrivate = $record->isPrivate;
		$this->urls = $record->urls;
		$this->type = $record->type;
		$this->like1 = $record->like1;
		$this->like2 = $record->like2;
		$this->like3 = $record->like3;
		$this->locked = $record->locked;
		$this->rating = $record->rating;
		$this->rating_game_id = $record->rating_game_id;
		$this->cluster_topic_id = $record->cluster_topic_id;
		$this->isCluster = $record->isCluster;
		$this->tags = $record->tags;

        $this->user = $record->user;
		$this->username = $record->username;
		$this->avatar = $record->avatar;
        $this->user_board_id = $record->user_board_id;
        $this->comments = $record->comments;
        $this->comment_first_id = $record->comment_first_id;
        $this->comment_last_id = $record->comment_last_id;
        $this->comment_first = $record->comment_first;
        $this->comment_last = $record->comment_last;
        $this->comment_first_user = $record->comment_first_user;
        $this->comment_last_user = $record->comment_last_user;
		$this->comment_first_username = $record->comment_first_username;
        $this->comment_last_username = $record->comment_last_username;
		$this->comment_first_avatar = $record->comment_first_avatar;
        $this->comment_last_avatar = $record->comment_last_avatar;
        $this->board = $record->board;
		$this->boardtype = $record->boardtype;
	}

    function Save()
	{
        if($this->board_id == 0)
            return;

        //if(strlen($this->title) == 0)
        //    return false;
		//$this->date_submit = time();

        if(!$this->isUrl($this->url))
            $this->url = '';
        $isnew = ($this->id == 0);

		$this->slug = url::title($this->filter($this->title), '-');
		if(strlen($this->slug) > 80)
			$this->slug = substr($this->slug, 0, 80);

		parent::Save();

		if($isnew)
        {
            $this->charid = Topic_Model::dec2any($this->id);
            parent::Save();
			if(!$this->isPrivate) //dont bump board if a PM
			{
				$board = new Board_Model();
				$board->Load($this->board_id);
				$board->AddTopic();
			}

			if($this->rating > 0){

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::ReviewGame;
			$log->Save();
			}

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::NewTopic;
			$log->Save();
        }
		else
		{
			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::EditTopic;
			$log->Save();
		}
	}

	function Delete()
	{
		if($this->id > 0)
		{
			//delete comments
			$comment = new Comment_Model();
			$comments = $comment->FindByTopic($this->id);
			foreach($comments as $comment)
			{
				$comment->Delete();
			}
			//delete urls
			$query = $this->db->query('DELETE FROM topics_urls WHERE topic_id = ?',$this->id);

			$query = $this->db->query('DELETE FROM topics_tags WHERE topic_id = ?',$this->id);
			//if($this->isCluster == 1)
			//	$query = $this->db->query('UPDATE topics SET cluster_topic_id = 0 WHERE cluster_topic_id = ?',$this->id);

			parent::Delete();

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::DeleteTopic;
			$log->Save();
		}
	}

    function Find($start=0, $boardid=0, $count=0, $sort=null, $dir='desc')
    {
        return $this->FindFinal($start,$boardid,0,0,$count);
    }

    function Load($id)
    {
        //$result = $this->FindFinal($id, 0);
		$result = $this->FindSingle($id);
		//echo Kohana::debug($result[0]);die();
		if(isset($result[0]))
			$this->setValues($result[0]);
    }
    function LoadWhere($id, $board_id, $property, $value, $where2)
    {
        $result = $this->FindFinal($id, $board_id, 0, 0, 1, $property, '=', mysql_real_escape_string($value), $where2);
        if(isset($result[0]))
            $this->setValues($result[0]);
    }
	function FindRelated()
	{
		if($this->isCluster)
		{
			return $this->FindFinal(0, 0, 0, 1, 10, 'cluster_topic_id', '=', $this->id, '', true);
		}
		elseif($this->cluster_topic_id > 0)
		{
			return $this->FindFinal(0, 0, 0, 1, 10, '', '', '', '(t.id='.$this->cluster_topic_id.' OR (t.id != '.$this->id.' AND cluster_topic_id = '.$this->cluster_topic_id.'))', true);
		}
		else return array();
	}
    function FindSince($since=0, $boardid=0, $count=0, $property='', $comparison='', $value='')
    {
        $since = intval($since);
        $where2 = ' AND t.date_submit > '.$since;
        return $this->FindFinal(0, $boardid, 0, 0, $count, $property, $comparison, $value, $where2);
    }
    function FindFinal($id=0, $boardid=0, $userid=0, $all=0, $count=0, $property='', $comparison='', $value='', $where2='', $ignorePrivacy=false)
    {
        $id = intval($id);
		$userid = intval($userid);
        $boardid = intval($boardid);
        if($id == 0 && $count > 0)
        {
            $count = intval($count);
            $limit = "LIMIT 0, $count";
        }
        else
            $limit = '';

            $where = ' WHERE 1=1';
        if($id > 0)
        {
            $where .= ' AND t.id = '.$id;
        }
        if(strlen($where2))
            $where .= " AND $where2 ";
        if(strlen($property) > 0 && strlen($comparison) > 0 && (is_array($value) || strlen($value) > 0))
        {
            if(($comparison == 'IN' || $comparison == 'NOT IN') && is_array($value))
            {
                $ids = implode(",",$value);
                $where .= " AND $property $comparison ($ids)";
            }
            else
                $where .= " AND $property $comparison '$value'";
        }

        $subs = '';
		if($userid == 0 && !$ignorePrivacy && $boardid > 0)
		{
			$where .= ' AND r.privacy <> 2 ';
		}
		else if($userid == 0 && !$ignorePrivacy)
		{
			$where .= ' AND r.privacy < 2 ';
		}

        if($boardid > 0){ //get a specific board
            $where .= ' AND r.id = '.$boardid;
			//hide topics you can't see
			if($userid > 0)
			{
				$subs = ' LEFT JOIN subscriptions s ON s.board_id = r.id AND s.user_id = '.$userid.' AND (r.privacy = 2 AND s.pending = 0) ';
				$where .= ' AND (1='.$this->isAdmin.' OR r.privacy < 2 OR r.privacy = 3 OR t.user_id = '.$userid.' OR (r.privacy = 2 AND s.board_id IS NOT NULL)) ';
			}
        }
        else if($userid > 0 && $all == 0) //join to get only boards subscribed to
        {
            $subs = ' JOIN subscriptions s ON s.board_id = r.id AND s.user_id = '.$userid.' AND (r.privacy < 2 || r.privacy = 3 || (r.privacy = 2 AND s.pending = 0)) ';
        }
        else if($userid > 0)//get all boards, excluding some
        {
			$subs = ' LEFT JOIN subscriptions s ON s.board_id = r.id AND s.user_id = '.$userid.' AND (r.privacy = 2 AND s.pending = 0) ';
			$subs .= ' LEFT JOIN subscription_users f ON f.poster_id = t.user_id AND f.user_id = '.$userid.' AND (f.pending = 0) ';
            $subs .= ' LEFT JOIN exclude_boards eb ON eb.board_id = r.id AND eb.user_id = '.$userid;
            $subs .= ' LEFT JOIN exclude_users eu ON eu.poster_id = t.user_id AND eu.user_id = '.$userid;
			$subs .= ' LEFT JOIN boards_banned bb ON bb.board_id = r.id AND bb.user_id = '.$userid;
            $where .= ' AND f.poster_id IS NOT NULL AND (1='.$this->isAdmin.' OR r.privacy < 2 OR t.user_id = '.$userid.' OR (r.privacy >= 2 AND s.board_id IS NOT NULL)) AND eb.board_id IS NULL AND eu.poster_id IS NULL AND bb.board_id IS NULL ';
        }
		if($all == 4)
		{
			//$where .= " t.user_id ) ";
		}
		$privacy = ' AND (t.isPrivate = 0 OR (t.isPrivate = 1 AND (t.user_id = '.$userid.' OR r.owner_id = '.$userid.')))';
		if($ignorePrivacy)
			$privacy = '';
		//GROUP_CONCAT(url.url) AS urls,  LEFT JOIN topics_urls url ON url.topic_id = t.id
        $q = 'SELECT t.id, t.title, t.body, t.slug, t.date_added, t.date_submit, t.charid, t.user_id, t.url, t.comments, t.comment_first_id,  t.comment_last_id, t.hidePublic, t.isPrivate, t.bodyRaw, t.like1, t.like2, t.like3, t.locked, t.rating, t.rating_game_id, t.cluster_topic_id, t.isCluster, t.tags, 
                u.title AS user, u.username as username, u.avatar as avatar, u.board_id as user_board_id,
                c1.comment AS comment_first, c2.comment AS comment_last,
                u1.title AS comment_first_user, u1.id AS comment_first_user_id, u1.username AS comment_first_username, u1.avatar AS comment_first_avatar, u2.id AS comment_last_user_id, u2.title AS comment_last_user,u2.avatar AS comment_last_avatar, u2.username AS comment_last_username,
                r.id AS board_id, coalesce(bu.title, g.title, r.title, \'noname\') as board, r.type as boardtype,
				t.urls, r.type
                FROM '.$this->dbTable.' t
                STRAIGHT_JOIN users u ON u.id = t.user_id
                STRAIGHT_JOIN boards r ON r.id = t.board_id
                LEFT JOIN comments c1 ON c1.id = t.comment_first_id
                LEFT JOIN comments c2 ON c2.id = t.comment_last_id
                LEFT JOIN users u1 ON u1.id = c1.user_id
                LEFT JOIN users u2 ON u2.id = c2.user_id
				LEFT JOIN users bu ON bu.board_id = r.id
				LEFT JOIN games g ON g.board_id = r.id
                '.$subs.'
                '.$where.$privacy.' ORDER BY t.date_submit DESC  '.$limit;
        //echo Kohana::debug($q);die();
        $query = $this->db->query($q);
        return $query->result(TRUE, 'Topic_Model');
    }
    
	function FindSingle($id)
    {
        $id = intval($id);
        $q = 'SELECT t.id, t.title, t.body, t.slug, t.date_added, t.date_submit, t.charid, t.url, t.user_id, t.url, t.comments, t.comment_first_id,  t.comment_last_id, t.hidePublic, t.isPrivate, t.bodyRaw, t.like1, t.like2, t.like3, t.locked, t.rating, t.rating_game_id, t.isCluster, t.cluster_topic_id, t.tags,
                u.title AS user, u.username AS username, u.avatar as avatar, u.board_id as user_board_id,
                c1.comment AS comment_first, c2.comment AS comment_last,
                u1.title AS comment_first_user, u1.id AS comment_first_user_id, u1.username AS comment_first_username, u1.avatar AS comment_first_avatar, u2.id AS comment_last_user_id, u2.title AS comment_last_user,u2.avatar AS comment_last_avatar, u2.username AS comment_last_username,
                r.id AS board_id, coalesce(bu.title, g.title, r.title, \'noname\') as board, r.type as boardtype,
				t.urls, t.type
                FROM '.$this->dbTable.' t
                JOIN users u ON u.id = t.user_id
                JOIN boards r ON r.id = t.board_id
                LEFT JOIN comments c1 ON c1.id = t.comment_first_id
                LEFT JOIN comments c2 ON c2.id = t.comment_last_id
                LEFT JOIN users u1 ON u1.id = c1.user_id
                LEFT JOIN users u2 ON u2.id = c2.user_id
				LEFT JOIN users bu ON bu.board_id = r.id
				LEFT JOIN games g ON g.board_id = r.id
                WHERE t.id = '.$id.' GROUP BY t.id ORDER BY t.date_submit DESC LIMIT 1';
        //echo Kohana::debug($q);die();
        $query = $this->db->query($q);
        return $query->result(TRUE, 'Topic_Model');
    }

	function FindByCategory($catid, $count=0)
	{
		$cat = new Category_Model;
		
		$format = '%1$s.id AS id, %1$s.name as name, %1$s.slug as slug, %1$s.description AS description, %1$s.date_added AS date_added, %1$s.website AS website, %1$s.twitter_name AS twitter_name, %1$s.image_url AS image_url';
		$select = sprintf($format, $this->dbTable);
		
		$this->db->select($select);
		$this->db->from($this->dbTable);
		$this->db->join('app_cats', 'app_cats.app_id = '.$this->dbTable.'.id');
		$this->db->join($cat->dbTable, $cat->dbTable.'.id = app_cats.cat_id');
		$this->db->where($cat->dbTable.'.id', $catid);
		return $this->db->get();
	}
	
	function AddCategory($catid)
	{
		$result = $this->db->insert('app_cats', array('app_id' => $this->id, 'cat_id' => $catid));
		return count($result);
	}

    function AddURLs($urls)
	{
		$urlstr = '';
		$seenurls = array();
        foreach($urls as $url)
        {
			$url = trim($url);
			if(strlen($url) > 0 && !in_array($url, $seenurls) && strlen($urls.','.$url) <= 500)
			{
				$query = $this->db->query('INSERT IGNORE INTO topics_urls (url, topic_id) VALUES (?,?)',array($url ,$this->id));
				$urlstr .= (strlen($urlstr) > 0) ? ','.$url : $url;
				$seenurls[] = $url;
			}
        }
		if(strlen($urlstr) > 0)
		{
			$this->urls = $urlstr;
			$this->Save();
		}
	}
	
    function AddTags($tags)
	{
        foreach($tags as $tag)
        {
			$tag = trim($tag);
			if(strlen($tag) > 0)
			{
				$query = $this->db->query('INSERT IGNORE INTO topics_tags (tag, topic_id, user_id, date_created) VALUES (?,?,?,?)',array($tag ,$this->id, $this->user_id, time()));
			}
        }
	}	

	function AddLike($type, $user_id)
	{
		if($this->db->where(array('user_id'=> $user_id, 'topic_id'=>$this->id))->count_records('topics_likes'))
			return;
		$logtype = 0;
		$updatedate = false;
		switch($type)
		{
			case 1:
				$logtype = Eventlog_Model::Like1;
				break;
			case 2:
				$logtype = Eventlog_Model::Like2;
				break;
			case 3:
				if($this->db->where(array('user_id'=> $user_id, 'type'=>3, 'date_liked >'=>time()-96400))->count_records('topics_likes'))
				{
					return;
					//$this->db->query('SELECT id FROM topics_likes where user_id = ? AND type = 3 AND date_liked < ?',$user_id, time()-86400);
				}					
				$logtype = Eventlog_Model::Like3;
				$updatedate = true;
				break;
			default:
				return;
		}
		$query = $this->db->query('INSERT IGNORE INTO topics_likes (type, user_id, topic_id, date_liked) VALUES (?,?,?,?)',array($type, $user_id ,$this->id, time()));
		$update = 'UPDATE `'.$this->dbTable.'` SET like'.$type.' = like'.$type.'+1';
		if($updatedate)
			$update .= ', date_submit = '.time();
		$query = $this->db->query($update.' WHERE `id` = ?',array($this->id)); //date_submit

		$log = new Eventlog_Model();
		$log->user_id = $user_id;
		$log->target_id = $this->id;
		$log->user2_id = $this->user_id;
		$log->type = $logtype;
		$log->Save();

	}
	
	function AddComment($id)
	{
        if($this->comments == 0){
            $query = $this->db->query('UPDATE `'.$this->dbTable."` SET date_submit = ?, comments = comments+1, comment_first_id = ? WHERE `id` = ?",array(time() ,$id, $this->id));
        }
        else{
            $query = $this->db->query('UPDATE `'.$this->dbTable."` SET date_submit = ?, comments = comments+1, comment_last_id = ? WHERE `id` = ?",array(time(), $id, $this->id));
        }
	}
	function UpdateComments()
	{
		$query = $this->db->query('UPDATE `'.$this->dbTable."` SET comments = (SELECT COUNT(1) AS comments FROM comments WHERE topic_id = ?), comment_first_id = (SELECT id FROM comments WHERE topic_id = ? ORDER BY date_added ASC LIMIT 1), comment_last_id = (SELECT id FROM comments where topic_id = ? ORDER BY date_added DESC LIMIT 1) WHERE `id` = ?",$this->id,$this->id,$this->id,$this->id);
	}
}
