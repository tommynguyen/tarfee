/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */

UPDATE engine4_activity_actiontypes
SET body='{item:$subject} created a new {item:$object:playlist} and added a video to this playlist'
WHERE module='ynvideo' AND TYPE ='ynvideo_add_video_new_playlist';