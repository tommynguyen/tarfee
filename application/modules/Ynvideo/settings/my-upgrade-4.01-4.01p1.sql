/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */

-- Update auth_view, auth_comment
UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]'
WHERE `type` = 'video' and `name` = 'auth_view';

UPDATE `engine4_authorization_permissions`  SET `params` = '["everyone","owner_network","owner_member_member","owner_member","parent_member","owner"]'
WHERE `type` = 'video' and `name` = 'auth_comment';

-- Delete menuitems in the main menu
DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'core_main_ynvideo';

DELETE FROM `engine4_core_menuitems`
WHERE `name` = 'core_admin_main_plugins_ynvideo';

-- Add permission for owner can remove video from his playlist
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'remove' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels`;
-- --------------------------------------------------------
--
-- Dumping data for table `engine4_authorization_permissions`
--
-- ADMIN, MODERATOR
-- video.max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'max' as `name`,
    3 as `value`,
    500 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- editing permission
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'ynvideo_playlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- video.max
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'video' as `type`,
    'max' as `name`,
    3 as `value`,
    100 as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');


