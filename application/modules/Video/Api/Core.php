<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Core.php 10212 2014-05-13 17:34:39Z andres $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Video_Api_Core extends Core_Api_Abstract
{
	
  public function uploadVideosChannel()
  {
  	// Call set_include_path() as needed to point to your client library.
	set_time_limit(0);
	require_once 'Google/autoload.php';
	require_once 'Google/Client.php';
	require_once 'Google/Service/YouTube.php';
	session_start();
	$htmlBody ="";
	/*
	 * You can acquire an OAuth 2.0 client ID and client secret from the
	 * Google Developers Console <https://console.developers.google.com/>
	 * For more information about using OAuth 2.0 to access Google APIs, please see:
	 * <https://developers.google.com/youtube/v3/guides/authentication>
	 * Please ensure that you have enabled the YouTube Data API for your project.
	 */
	$settings = Engine_Api::_()->getApi('settings', 'core');
	$user_youtube_allow = $settings->getSetting('user_youtube_allow');
	if(!$user_youtube_allow)
	{
		return false;
	}
	$token = $settings->getSetting('user_youtube_token', "");
	$OAUTH2_CLIENT_ID = $settings->getSetting('user_youtube_clientid', "");
	$OAUTH2_CLIENT_SECRET = $settings->getSetting('user_youtube_secret', "");
	
	if(empty($token) || empty($token) || empty($token)) {
		return fasle;
	}
	
	//getting videos
	$videoTable = Engine_Api::_() -> getItemTable('video');
  	$select = $videoTable -> select() -> where('file_id <> ?', '0') -> limit(2);
  	$videos = $videoTable -> fetchAll($select);
	
	
	foreach($videos as $videoLoop) {
		$client = new Google_Client();
		$client->setClientId($OAUTH2_CLIENT_ID);
		$client->setClientSecret($OAUTH2_CLIENT_SECRET);
		$client->setAccessType('offline');
		$client->setScopes('https://www.googleapis.com/auth/youtube');
		$redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
		    FILTER_SANITIZE_URL);
		$redirect = str_replace("index.php", "admin/user/youtube/token", $redirect);   
		$client->setRedirectUri($redirect);
		$client->setAccessToken($token);
		
		/**
         * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
         */
        if($client->isAccessTokenExpired()) {
            $newToken = json_decode($client->getAccessToken());
            $client->refreshToken($newToken->refresh_token);
            $settings->setSetting('user_youtube_token', $client->getAccessToken());
        }
		
		// Check to ensure that the access token was successfully acquired.
		if ($client->getAccessToken()) {
		  try{
		  	
	        // Define an object that will be used to make all API requests.
			$youtube = new Google_Service_YouTube($client);
		  	
		  	//get info of files
		  	$file = Engine_Api::_() -> getItem('storage_file', $videoLoop -> file_id);
		  	if(!file) {
		  		continue;
		  	}
		  	
		    // REPLACE this value with the path to the file you are uploading.
		    $videoPath = $file -> storage_path;
		
		    // Create a snippet with title, description, tags and category ID
		    // Create an asset resource and set its snippet metadata and type.
		    // This example sets the video's title, description, keyword tags, and
		    // video category.
		    $snippet = new Google_Service_YouTube_VideoSnippet();
		    $snippet->setTitle($videoLoop -> getTitle());
		    $snippet->setDescription($videoLoop -> getDescription());
		
		    // Numeric video category. See
		    // https://developers.google.com/youtube/v3/docs/videoCategories/list 
		    $snippet->setCategoryId("22");
		
		    // Set the video's status to "public". Valid statuses are "public",
		    // "private" and "unlisted".
		    $status = new Google_Service_YouTube_VideoStatus();
		    $status->privacyStatus = "public";
		
		    // Associate the snippet and status objects with a new video resource.
		    $video = new Google_Service_YouTube_Video();
		    $video->setSnippet($snippet);
		    $video->setStatus($status);
		
		    // Specify the size of each chunk of data, in bytes. Set a higher value for
		    // reliable connection as fewer chunks lead to faster uploads. Set a lower
		    // value for better recovery on less reliable connections.
		    $chunkSizeBytes = 1 * 1024 * 1024;
		
		    // Setting the defer flag to true tells the client to return a request which can be called
		    // with ->execute(); instead of making the API call immediately.
		    $client->setDefer(true);
		
		    // Create a request for the API's videos.insert method to create and upload the video.
		    $insertRequest = $youtube->videos->insert("status,snippet", $video);
		
		    // Create a MediaFileUpload object for resumable uploads.
		    $media = new Google_Http_MediaFileUpload(
		        $client,
		        $insertRequest,
		        'video/*',
		        null,
		        true,
		        $chunkSizeBytes
		    );
		    $media->setFileSize(filesize($videoPath));
		
		
		    // Read the media file and upload it chunk by chunk.
		    $status = false;
		    $handle = fopen($videoPath, "rb");
		    while (!$status && !feof($handle)) {
		      $chunk = fread($handle, $chunkSizeBytes);
		      $status = $media->nextChunk($chunk);
		    }
		
		    fclose($handle);
		
		    // If you want to make other calls after the file upload, set setDefer back to false
		    $client->setDefer(false);
			
			//update video data
			$videoLoop -> code = $status['id'];
			$videoLoop -> file_id = 0;
			$videoLoop -> type = 1;
			$videoLoop -> status = 1;
			$videoLoop -> save();
			
			//delete old video
			$file -> delete();
			
			//echo $status['id'];
								
		    //$htmlBody .= "<h3>Video Uploaded</h3><ul>";
		    //$htmlBody .= sprintf('<li>%s (%s)</li>',
		    //    $status['snippet']['title'],
		    //    $status['id']);
		    $htmlBody .= '</ul>';
		  } catch (Google_Service_Exception $e) {
		    //$htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
		    //    htmlspecialchars($e->getMessage()));
		    continue;
		  } catch (Google_Exception $e) {
		    //$htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
		    //    htmlspecialchars($e->getMessage()));
		    continue;
		  }
		}
	}
	//echo $htmlBody;
  }
  	
  public function getVideosPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getVideosSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  public function getVideosSelect($params = array())
  {
    $table = Engine_Api::_()->getDbtable('videos', 'video');
    $rName = $table->info('name');

    $tmTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
    $tmName = $tmTable->info('name');
    
    $select = $table->select()
      ->from($table->info('name'))
      ->order( !empty($params['orderby']) ? $params['orderby'].' DESC' : "$rName.creation_date DESC" );
    
    if( !empty($params['text']) ) {
      $searchTable = Engine_Api::_()->getDbtable('search', 'core');
      $db = $searchTable->getAdapter();
      $sName = $searchTable->info('name');
      $select
        ->joinRight($sName, $sName . '.id=' . $rName . '.video_id', null)
        ->where($sName . '.type = ?', 'video')
        ->where(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (? IN BOOLEAN MODE)', $params['text'])))
        //->order(new Zend_Db_Expr($db->quoteInto('MATCH(' . $sName . '.`title`, ' . $sName . '.`description`, ' . $sName . '.`keywords`, ' . $sName . '.`hidden`) AGAINST (?) DESC', $params['text'])))
        ;
    }
      
    if( !empty($params['status']) && is_numeric($params['status']) )
    {
      $select->where($rName.'.status = ?', $params['status']);
    }
    if( !empty($params['search']) && is_numeric($params['search']) )
    {
      $select->where($rName.'.search = ?', $params['search']);
    }
    if( !empty($params['user_id']) && is_numeric($params['user_id']) )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']);
    }

    if( !empty($params['user']) && $params['user'] instanceof User_Model_User )
    {
      $select->where($rName.'.owner_id = ?', $params['user_id']->getIdentity());
    }
    
    if( !empty($params['category']) )
    {
      $select->where($rName.'.category_id = ?', $params['category']);
    }

    if( !empty($params['tag']) )
    {
      $select
        // ->setIntegrityCheck(false)
        // ->from($rName)
        ->joinLeft($tmName, "$tmName.resource_id = $rName.video_id", NULL)
        ->where($tmName.'.resource_type = ?', 'video')
        ->where($tmName.'.tag_id = ?', $params['tag']);
    }

    return $select;
  }

  public function getCategories()
  {
    $table = Engine_Api::_()->getDbTable('categories', 'video');
    return $table->fetchAll($table->select()->order('category_name ASC'));
  }

  public function getCategory($category_id)
  {
    return Engine_Api::_()->getDbtable('categories', 'video')->find($category_id)->current();
  }

  public function getRating($video_id)
  {
    $table  = Engine_Api::_()->getDbTable('ratings', 'video');
    $rating_sum = $table->select()
      ->from($table->info('name'), new Zend_Db_Expr('SUM(rating)'))
      ->group('video_id')
      ->where('video_id = ?', $video_id)
      ->query()
      ->fetchColumn(0)
      ;

    $total = $this->ratingCount($video_id);
    if ($total) $rating = $rating_sum/$this->ratingCount($video_id);
    else $rating = 0;
    
    return $rating;
  }

  public function getRatings($video_id)
  {
    $table  = Engine_Api::_()->getDbTable('ratings', 'video');
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.video_id = ?', $video_id);
    $row = $table->fetchAll($select);
    return $row;
  }
  
  public function checkRated($video_id, $user_id)
  {
    $table  = Engine_Api::_()->getDbTable('ratings', 'video');

    $rName = $table->info('name');
    $select = $table->select()
                 ->setIntegrityCheck(false)
                    ->where('video_id = ?', $video_id)
                    ->where('user_id = ?', $user_id)
                    ->limit(1);
    $row = $table->fetchAll($select);
    
    if (count($row)>0) return true;
    return false;
  }

  public function setRating($video_id, $user_id, $rating){
    $table  = Engine_Api::_()->getDbTable('ratings', 'video');
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.video_id = ?', $video_id)
                    ->where($rName.'.user_id = ?', $user_id);
    $row = $table->fetchRow($select);
    if (empty($row)) {
      // create rating
      Engine_Api::_()->getDbTable('ratings', 'video')->insert(array(
        'video_id' => $video_id,
        'user_id' => $user_id,
        'rating' => $rating
      ));
    }
/*
    $select = $table->select()
      //->setIntegrityCheck(false)
      ->from($rName)
      ->where($rName.'.video_id = ?', $video_id);

    $row = $table->fetchAll($select);
    $total = count($row);
    foreach( $row as $item )
    {
      $rating += $item->rating;
    }
    $video = Engine_Api::_()->getItem('video', $video_id);
    $video->rating = $rating/$total;
    $video->save();*/
    
  }

  public function ratingCount($video_id){
    $table  = Engine_Api::_()->getDbTable('ratings', 'video');
    $rName = $table->info('name');
    $select = $table->select()
                    ->from($rName)
                    ->where($rName.'.video_id = ?', $video_id);
    $row = $table->fetchAll($select);
    $total = count($row);
    return $total;
  }

  // handle video upload
  public function createVideo($params, $file, $values)
  {
    if( $file instanceof Storage_Model_File ) {
      $params['file_id'] = $file->getIdentity();
    } else {
      // create video item
      $video = Engine_Api::_()->getDbtable('videos', 'video')->createRow();
      $file_ext = pathinfo($file['name']);
      $file_ext = $file_ext['extension'];
      $video->code = $file_ext;
      $video->save();

      // Store video in temporary storage object for ffmpeg to handle
      $storage = Engine_Api::_()->getItemTable('storage_file');
      $storageObject = $storage->createFile($file, array(
        'parent_id' => $video->getIdentity(),
        'parent_type' => $video->getType(),
        'user_id' => $video->owner_id,
      ));

      // Remove temporary file
      @unlink($file['tmp_name']);

      $video->file_id = $storageObject->file_id;
      $video->save();

      // Add to jobs
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.html5', false)) {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
          'video_id' => $video->getIdentity(),
          'type' => 'mp4',
        ));
      } else {
        Engine_Api::_()->getDbtable('jobs', 'core')->addJob('video_encode', array(
          'video_id' => $video->getIdentity(),
          'type' => 'flv',
        ));
      }
    }

    return $video;
  }

  public function deleteVideo($video)
  {

    // delete video ratings
    Engine_Api::_()->getDbtable('ratings', 'video')->delete(array(
      'video_id = ?' => $video->video_id,
    ));

    // check to make sure the video did not fail, if it did we wont have files to remove
    if ($video->status == 1){
      // delete storage files (video file and thumb)
      if ($video->type == 3) Engine_Api::_()->getItem('storage_file', $video->file_id)->remove();
      if ($video->photo_id) Engine_Api::_()->getItem('storage_file', $video->photo_id)->remove();
    }
    
    // delete activity feed and its comments/likes
    $item = Engine_Api::_()->getItem('video', $video->video_id);
    if ($item) {
      $item->delete();
    }


  }
}
