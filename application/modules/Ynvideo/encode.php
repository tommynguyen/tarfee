<?php
//http://dev2.younetco.com/dev/mobile_view/?m=lite&name=encode&module=ynvideo
//backup database + public video before migrate

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');

while (1)
{
	//get all video FLV
	$stTable = Engine_Api::_() -> getDbTable('files', 'storage');
	$select = $stTable -> select() -> where('parent_type = ?', 'video') -> where("`extension` = 'flv'") -> limit(3);
	$videos = $stTable -> fetchAll($select);
	$translate = Zend_Registry::get('Zend_Translate');
	if (count($videos))
	{
		foreach ($videos as $video)
		{
			$video = Engine_Api::_() -> getItem('video', $video -> parent_id);
			if ($video -> status != 1)
			{
				continue;
			}
			// Make sure FFMPEG path is set
			$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> ynvideo_ffmpeg_path;
			if (!$ffmpeg_path)
			{
				throw new Ynvideo_Model_Exception('Ffmpeg not configured');
			}
			// Make sure FFMPEG can be run
			if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
			{
				$output = null;
				$return = null;
				exec($ffmpeg_path . ' -version', $output, $return);
				if ($return > 0)
				{
					throw new Ynvideo_Model_Exception('Ffmpeg found, but is not executable');
				}
			}

			// Check we can execute
			if (!function_exists('shell_exec'))
			{
				throw new Ynvideo_Model_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
			}

			// Check the video temporary directory
			$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'video';
			if (!is_dir($tmpDir))
			{
				if (!mkdir($tmpDir, 0777, true))
				{
					throw new Ynvideo_Model_Exception('Video temporary directory did not exist and could not be created.');
				}
			}
			if (!is_writable($tmpDir))
			{
				throw new Ynvideo_Model_Exception('Video temporary directory is not writable.');
			}

			if (!($video instanceof Ynvideo_Model_Video))
			{
				throw new Ynvideo_Model_Exception('Argument was not a valid video');
			}

			// Update to encoding status
			$video -> status = 2;
			$video -> type = Ynvideo_Plugin_Factory::getUploadedType();
			$video -> save();

			// Prepare information
			$owner = $video -> getOwner();
			$filetype = $video -> code;

			// Pull video from storage system for encoding
			$storageObject = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
			if (!$storageObject)
			{
				throw new Ynvideo_Model_Exception('Video storage file was missing');
			}

			$originalPath = $storageObject -> temporary();
			if (!file_exists($originalPath))
			{
				throw new Ynvideo_Model_Exception('Could not pull to temporary file');
			}

			$outputPath_mpeg4 = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vconverted_mpg4.mp4';
			$outputPath_h264 = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_vconverted_h264.mp4';
		
			//Convert to Mp4 (h264 - HTML5, mpeg4 - IOS)
			$mpeg4_videoCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($originalPath) . ' ' . '-acodec aac -strict experimental -vcodec mpeg4 -b 2000k -mbd 2 -cmp 2 -subcmp 2 -s 640x360 -y ' . escapeshellarg($outputPath_mpeg4) . ' 2>&1';
			$h264_videoCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($originalPath) . ' ' . '-acodec libfaac -ab 96k -vcodec libx264  -level 21 -refs 2 -threads 0 -s 640x360 -y ' . escapeshellarg($outputPath_h264) . ' ';
		
			// Prepare output header
			$output = PHP_EOL;
			$output .= $originalPath . PHP_EOL;
			$output .= $outputPath_h264 . PHP_EOL;
			$output .= $outputPath_mpeg4 . PHP_EOL;
			//$output .= $outputPath_ogg . PHP_EOL;

			// Execute video encode command
			$videoOutput_mpeg4 = $output . $mpeg4_videoCommand . PHP_EOL . shell_exec($mpeg4_videoCommand);
			$videoOutput_h264 = $output . $h264_videoCommand . PHP_EOL . shell_exec($h264_videoCommand);
			// Check for failure
			$success = true;

			// Unsupported format
			if (preg_match('/Unknown format/i', $videoOutput_mpeg4) || preg_match('/Unsupported codec/i', $videoOutput_mpeg4) || preg_match('/patch welcome/i', $videoOutput_mpeg4) || preg_match('/Audio encoding failed/i', $videoOutput_mpeg4) || !is_file($outputPath_mpeg4) || filesize($outputPath_mpeg4) <= 0)
			{
				$success = false;
				$video -> status = 3;
			}

			// This is for audio files
			else
			if (preg_match('/video:0kB/i', $videoOutput_mpeg4))
			{
				$success = false;
				$video -> status = 5;
			}

			// Failure
			if (!$success)
			{
				$db = $video -> getTable() -> getAdapter();
				$db -> beginTransaction();
				try
				{
					$video -> save();
					$db -> commit();
				}
				catch (Exception $e)
				{
					$videoOutput_mpeg4 .= PHP_EOL . $e -> __toString() . PHP_EOL;
					$db -> rollBack();
				}

				// Write to additional log in dev
				if (APPLICATION_ENV == 'development')
				{
					file_put_contents($tmpDir . '/' . $video -> video_id . '.txt', $videoOutput_mpeg4);
				}
			}

			// Success
			else
			{
				// Save video and thumbnail to storage system
				$params = array(
					'parent_id' => $video -> getIdentity(),
					'parent_type' => $video -> getType(),
					'user_id' => $video -> owner_id
				);

				$db = $video -> getTable() -> getAdapter();
				$db -> beginTransaction();

				try
				{
					$storageObject -> setFromArray($params);
					$storageObject -> store($outputPath_h264);

					// Store h264 video in temporary storage object for ffmpeg to handle
					$newObject = Engine_Api::_() -> storage() -> create($outputPath_mpeg4, $params);
					$db -> commit();
				}
				catch (Exception $e)
				{
					$db -> rollBack();

					// delete the files from temp dir
					unlink($originalPath);
					unlink($outputPath_mpeg4);
					unlink($outputPath_h264);
					//unlink($outputPath_ogg);

					$video -> status = 7;
					$video -> save();

					throw $e;
					// throw
				}
				$video -> file1_id = $newObject -> file_id;
				//$video -> file2_id = $oggObject -> file_id;
				$video -> status = 1;
				$video -> save();

				// delete the files from temp dir
				unlink($originalPath);
				unlink($outputPath_mpeg4);
				unlink($outputPath_h264);
				//unlink($outputPath_ogg);
				echo $translate -> translate("Processed: ") . $video -> title . $translate -> translate(" - done <br/>");
			}

		}
	}
	else
	{
		echo $translate -> translate("Processed successfully!");
		break;
	}
}
