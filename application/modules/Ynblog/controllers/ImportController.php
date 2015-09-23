<?php
require_once ('YnsRSSFeed/YnsRSS.php');
class Ynblog_ImportController extends Core_Controller_Action_Standard {
	public function importAction() {
		// Checking authorization
		if (! $this->_helper->requireUser ()->isValid ())
			return;
		if (! $this->_helper->requireAuth ()->setAuthParams ( 'blog', null, 'create' )->isValid ())
			return;
		$this->view->system_id = 0;

		// Get navigation
		$check_ynblog = Engine_Api::_ ()->ynblog ()->getYnBlog ();
		if (! $check_ynblog) {
			$this->view->navigation = $navigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'blog_main' );
		} else {
			$this->view->navigation = $navigation = Engine_Api::_ ()->getApi ( 'menus', 'core' )->getNavigation ( 'ynblog_main' );
		}
		// Get auto-approved settings
		$is_approved = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.moderation', 0 ) ? 0 : 1;
		// Get max blogs number
		$max_blogs = Engine_Api::_ ()->getItemTable ( 'blog' )->checkMaxBlogs ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$blog_number = Engine_Api::_ ()->getItemTable ( 'blog' )->getCountBlog ( $viewer );
		if ($max_blogs == 0 || $blog_number < $max_blogs) {
			$maximum_reach = false;
		} else {
			$maximum_reach = true;
		}

		$this->view->maximum_reach = $maximum_reach;
		$this->view->max_blogs = $max_blogs;
		// Prepare form
		$this->view->form = $form = new Ynblog_Form_Importer ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$used_link = Engine_Api::_ ()->getDbTable ( 'links', 'ynblog' )->getLink ( $viewer->getIdentity () );
		if (! empty ( $used_link )) {
			$form->populate ( array (
					'url' => $used_link->link_url
			) );
		}
		// If not post or form not valid, return
		if (! $this->getRequest ()->isPost ()) {
			return;
		}
		if (! $form->isValid ( $this->getRequest ()->getPost () )) {
			return;
		}

		// Process
		//
		// Get user info & import form values
		$user = Engine_Api::_ ()->user ()->getViewer ();
		$values = $form->getValues ();
		$table = Engine_Api::_ ()->getItemTable ( 'blog' );

		// Check if import system is from URL
		if ($values ['system'] == 4) {
			$result = $this->readURL ( $values, $user->getIdentity () );
			switch ($result) {
				case 'existing_URL' :
					$form->addError ( 'The URL has already been used. Please choose another one.' );
					$this->view->system_id = 4;
					return;
				case 'invalid_URL' :
					$form->addError ( 'The URL must have XML extension and not empty.' );
					$this->view->system_id = 4;
					return;
				case 'completed' :
					return $this->_helper->redirector->gotoRoute ( array (
							'action' => 'manage'
					), 'blog_general' );
			}
		}

		$userName = $values ['username'];

		// Transaction
		$db = $table->getAdapter ();
		$db->beginTransaction ();

		// Auth
		$auth = Engine_Api::_ ()->authorization ()->context;
		$roles = array (
				'owner',
				'owner_member',
				'owner_member_member',
				'owner_network',
				'registered',
				'everyone'
		);

		if (empty ( $values ['auth_view'] )) {
			$values ['auth_view'] = 'everyone';
		}

		if (empty ( $values ['auth_comment'] )) {
			$values ['auth_comment'] = 'everyone';
		}
		$viewMax = array_search ( $values ['auth_view'], $roles );
		$commentMax = array_search ( $values ['auth_comment'], $roles );
		$viewer = $this->_helper->api ()->user ()->getViewer ();
		$user_id = $viewer->getIdentity ();
		$b_table = Engine_Api::_ ()->getItemTable ( 'blog' );
		$select = $b_table->select ()->where ( 'owner_id = ?', $user_id );
		$Blogs = $b_table->fetchAll ( $select );
		try {
			if (! empty ( $values ['filexml'] )) {
				$file = $form->getElement ( 'filexml' );
				$path = $file->getDestination () . DIRECTORY_SEPARATOR . $file->getValue ();

				// WordPress Sysem
				if ($values ['system'] == "1") {
					if ($path != "") {
						$oDoc = new DOMDocument ();
						@$oDoc->load ( $path );
						$eChanels = $oDoc->getElementsByTagName ( "channel" );
						$aTitles = array ();
                        $aLinks = array ();
						$aTexts = array ();
						$aDate = array ();
						$bFlag = false;

						foreach ( $eChanels as $eChannel ) {
							$eItemss = $eChannel->getElementsByTagName ( "item" );
							$bFlag = true;
							foreach ( $eItemss as $eItems ) {
								// Get content
								$eContents = $eItems->getElementsByTagName ( "encoded" );
								$sContent = $eContents->item ( 0 )->nodeValue;
								if ($sContent != "") {
									// Get title
									$eTitles = $eItems->getElementsByTagName ( "title" );
									$sTitle = $eTitles->item ( 0 )->nodeValue;
                                    $eLinks = $eItems->getElementsByTagName ( "link" );
                                    $sLink = $eLinks->item ( 0 )->nodeValue;
									$eDate = $eItems->getElementsByTagName ( "post_date" );
									$sDate = $eDate->item ( 0 )->xxxsnodeValue;
									$aTitles [] = $sTitle;
                                    $aLinks[] = $sLink;
									$aTexts [] = "<pre width = '93'>" . $sContent . "</pre>";
									$aDate [] = $sDate;
								}
							}
						}

						if ($bFlag == false) {
							$form->addError ( 'Import error or no entry was gotten!' );
							$form->system->setValue ( $values ['system'] );
							$this->view->system_id = $values ['system'];
							return;
						}
						// fix for blog-160
						$count = $blog_number;
						//print_r($count);die;
						for($i = count ( $aTitles ) - 1; $i >= 0; $i --) {
							if ($max_blogs != 0 && $count >= $max_blogs) {
								break;
							}
							$bCheck = true;
							foreach ( $Blogs as $blog ) {
								if ($blog->pub_date == $aDate [$i] && $blog->title == $aTitles [$i]) {
									$bCheck = false;
								}
							}

							if ($bCheck == true) { // Insert the blog entry into the
							                       // database
								$row = $table->createRow ();
								$row->owner_id = $user->getIdentity ();
								$row->owner_type = $user->getType ();
								$row->pub_date = $aDate [$i];
								$row->creation_date = date ( 'Y-m-d H:i:s' );
								$row->modified_date = date ( 'Y-m-d H:i:s' );
								$row->title = $aTitles [$i];
                                $row->link_detail = $aLinks [$i];
								$row->body = $aTexts [$i];
								// trunglt - fix for blog-160
								$row->is_approved = $is_approved;
								// Add activity if the blog is approved at the
								// first time
								if ($row->is_approved) {
									$row->add_activity = 1;
								}
								$row->save ();
								// trunglt
								$count ++;
								foreach ( $roles as $j => $role ) {
									$auth->setAllowed ( $row, $role, 'view', ($j <= $viewMax) );
									$auth->setAllowed ( $row, $role, 'comment', ($j <= $commentMax) );
								}
								if ($row->is_approved) {
									$owner = $row->getParent ();
									$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $owner, $row, 'ynblog_import' );

									// Make sure action exists before attaching
									// the blog
									// to the activity
									if ($action) {
										Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $row );
									}

									// Send notifications for subscribers
									Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $row );
								}
							}
						}
						$db->commit ();
					} else {
						return;
					}
				}

				// Blogger System
				if ($values ['system'] == "2") {
					if ($path != "") {
						$oDoc = new DOMDocument ();
						@$oDoc->load ( $path );
						$aTitles = array ();
						$aTexts = array ();
						$aDate = array ();
						$bFlag = false;
						$eEntrys = $oDoc->getElementsByTagName ( "entry" );
						foreach ( $eEntrys as $eEntry ) {
							$eCategories = $eEntry->getElementsByTagName ( "category" );
							$bFlag = true;
							$sTerm = $eCategories->item ( 0 )->getAttribute ( 'term' );
							if (strpos ( $sTerm, 'kind#post' )) {
								// getcontent
								$eContents = $eEntry->getElementsByTagName ( "content" );
								$sContent = $eContents->item ( 0 )->nodeValue;
								if ($sContent != "") {
									// get title
									$eTitles = $eEntry->getElementsByTagName ( "title" );
									$sTitle = $eTitles->item ( 0 )->nodeValue;
									$aTitles [] = $sTitle;
									$aTexts [] = "<pre width = '93'>" . $sContent . "</pre>";
									$eDate = $eEntry->getElementsByTagName ( "published" );
									$sDate = $eDate->item ( 0 )->nodeValue;
									$aDate [] = $sDate;
								}
							}
						}
						if ($bFlag == false) {
							$form->addError ( 'Import error or not entry was gotten!' );
							$form->system->setValue ( $values ['system'] );
							$this->view->system_id = $values ['system'];
							return;
						}

						// trunglt
						$blogger_count = $blog_number;

						for($i = 0; $i < count ( $aTitles ); $i ++) {
							if ($max_blogs != 0 && $blogger_count >= $max_blogs) {
								break;
							}
							$bCheck = true;
							foreach ( $Blogs as $blog ) {
								if ($blog->pub_date == $aDate [$i] && $blog->title == $aTitles [$i]) {
									$bCheck = false;
								}
							}
							if ($bCheck == true) {
								// insert the blog entry into the database
								$row = $table->createRow ();
								$row->owner_id = $user->getIdentity ();
								$row->owner_type = $user->getType ();
								$row->pub_date = $aDate [$i];
								$row->creation_date = date ( 'Y-m-d H:i:s' );
								$row->modified_date = date ( 'Y-m-d H:i:s' );
								$row->title = $aTitles [$i];
								$row->body = $aTexts [$i];
								// trunglt - fix for blog-160
								$row->is_approved = $is_approved;
								if ($row->is_approved) {
									$row->add_activity = 1;
								}

								$row->save ();
								// trunglt
								$blogger_count ++;

								foreach ( $roles as $j => $role ) {
									$auth->setAllowed ( $row, $role, 'view', ($j <= $viewMax) );
									$auth->setAllowed ( $row, $role, 'comment', ($j <= $commentMax) );
								}
								if ($row->is_approved) {
									$owner = $row->getParent ();
									$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $owner, $row, 'ynblog_import' );

									// Make sure action exists before attaching the blog
									// to the activity
									if ($action) {
										Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $row );
									}

									// Send notifications for subscribers
									Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $row );

								}
							}
						}
						$db->commit ();
					} else
						return;
				}
			}

			// Tumblr System
			else if (! empty ( $userName ) && $values ['system'] == 3) {
				$i = 0;
				$aPosts = array ();
				$sFeed = '';
				$userName = str_replace ( ".tumblr.com", "", $userName );
				if (!preg_match('/^[\w-]*$/', $userName)) {
					$form->addError ( 'Invalid Tumblr blog name.' );
					$form->system->setValue ( $values ['system'] );
					$this->view->system_id = $values ['system'];
					return;
				}

				$sUrl = "";
				do {
					$sUrl = 'http://' . $userName . '.tumblr.com/api/read?start=' . $i . '&num=50';
					$oFile = @file_get_contents ( $sUrl );
					if ($oFile) {
						$sFeed = new SimpleXMLElement ( $oFile );
						$aPosts [] = array_merge ( $aPosts, $sFeed->xpath ( 'posts//post' ) );
						$i = ( int ) $sFeed->posts->attributes ()->start + 50;
					} else {
						$form->addError ( 'Username does not exist!' );
						$form->system->setValue ( $values ['system'] );
						$this->view->system_id = $values ['system'];
						return;
					}
				} while ( $i <= ( int ) $sFeed->posts ["total"] );
				//print_r($sUrl);die;
				$aTitles = array ();
				$aTexts = array ();
				$aDate = array ();
				$bFlag = false;
				foreach ( $aPosts [0] as $ePost ) {
					$bFlag = true;
					switch ($ePost->attributes ()->type) {
						case "regular" :
							$aTitles [] = htmlspecialchars ( $ePost->{'regular-title'} );
							$sTxt = $this->formatForSE ( $ePost->{'regular-body'} );
							$sTxt = str_replace ( array (
									'<br/>',
									'<br>',
									'<br />'
							), array (
									'<p ></p>',
									'<p></p>',
									'<p></p>'
							), $sTxt );
							$aTexts [] = $sTxt;
							break;
						case "photo" :
							$aTitles [] = "Photos";
							$aTexts [] = "<img src=" . $ePost->{'photo-url'} . " alt=''/><br/><br/>" . $this->formatForSE ( $ePost->{'photo-caption'} ) . "]";
							break;
						case "quote" :
							$aTitles [] = htmlspecialchars ( strip_tags ( $ePost->{'quote-text'} ) );
							$aTexts [] = $ePost->{'quote-text'} . "<br/>" . $this->formatForSE ( $ePost->{'quote-source'} );
							break;
						case "link" :
							$aTitles [] = htmlspecialchars ( strip_tags ( $ePost->{'link-text'} ) );
							$aTexts [] = "<a href='" . $ePost->{'link-url'} . "'>" . $ePost->{'link-text'} . "</a><br/>" . $this->formatForSE ( $ePost->{'link-description'} );
							break;
						case "conversation" :
							$aTitles [] = htmlspecialchars ( strip_tags ( $ePost->{'conversation-title'} ) );
							$sTemp = '';
							foreach ( $ePost->{'conversation-line'} as $line ) {
								$sTemp .= "<strong>" . $line->attributes ()->label . "</strong>" . $line . "<br/>";
							}
							$aTexts [] = $sTemp;
							break;
					}
				}
				if ($bFlag == false) {
					$form->addError ( 'Import error or no entry was gotten!' );
					$form->system->setValue ( $values ['system'] );
					$this->view->system_id = $values ['system'];
					return;
				}
				// @todo trunglt
				$tumble_count = $blog_number;
				for($i = 0; $i < count ( $aTitles ); $i ++) {
					if ($max_blogs != 0 && $tumble_count >= $max_blogs) {
						break;
					}
					$bCheck = true;
					foreach ( $Blogs as $blog ) {
						if ($blog->pub_date == @$aDate [$i] && $blog->title == $aTitles [$i]) {
							$bCheck = false;
						}
					}
					if ($bCheck == true) {
						// insert the blog entry into the database
						$row = $table->createRow ();
						$row->owner_id = $user->getIdentity ();
						$row->owner_type = $user->getType ();
						$row->pub_date = @$aDate [$i];
						$row->creation_date = date ( 'Y-m-d H:i:s' );
						$row->modified_date = date ( 'Y-m-d H:i:s' );
						$row->title = $aTitles [$i];
						$row->body = "<pre width = '93'>" . $aTexts [$i] . "</pre>";
						// trunglt - fix for blog-160
						$row->is_approved = $is_approved;
						if ($row->is_approved) {
							$row->add_activity = 1;
						}
						$row->save ();
						$tumble_count ++;
						foreach ( $roles as $j => $role ) {
							$auth->setAllowed ( $row, $role, 'view', ($j <= $viewMax) );
							$auth->setAllowed ( $row, $role, 'comment', ($j <= $commentMax) );
						}
						if ($row->is_approved) {
							$owner = $row->getParent ();
							$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $owner, $row, 'ynblog_import' );

							// Make sure action exists before attaching the blog
							// to the activity
							if ($action) {
								Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $row );
							}

							// Send notifications for subscribers
							Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $row );
						}
					}
				}
				$db->commit ();
			} else {
				$form->addError ( 'Please choose a file XML, enter a username or add an URL!' );
				$form->system->setValue ( $values ['system'] );
				$this->view->system_id = $values ['system'];
				return;
			}
		}

		catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		return $this->_helper->redirector->gotoRoute ( array (
				'action' => 'manage'
		), 'blog_general' );
	}
	public function readURL($values = array(), $user_id = 0) {
		// Checkin
		if ($user_id == 0)
			return;
		if ($values ['system'] != 4)
			return;
		$rss = new YnsRSS ();
		$feed = $rss->getParse ( null, $values ['url'], null );

		// Checking valid URL ( XML extension and nt empty)
		if (empty ( $feed ['entries'] )) {
			return 'invalid_URL';
		}

		// Checking existing URL
		if (! Engine_Api::_ ()->ynblog ()->checkURL ( $values ['url'], $user_id )) {
			return 'existing_URL';
		}

		// Get auto-approved settings
		$is_approved = Engine_Api::_ ()->getApi ( 'settings', 'core' )->getSetting ( 'ynblog.moderation', 0 ) ? 0 : 1;
		// Get max blogs number
		$max_blogs = Engine_Api::_ ()->getItemTable ( 'blog' )->checkMaxBlogs ();
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$blog_number = Engine_Api::_ ()->getItemTable ( 'blog' )->getCountBlog ( $viewer );

		$link_table = Engine_Api::_ ()->getDbTable ( 'links', 'ynblog' );
		$link_select = $link_table->select ()->where ( 'user_id = ?', $user_id );
		$link = $link_table->fetchRow ( $link_select );

		if ($link) {
			$link->link_url = $values ['url'];
            $link->last_run = new Zend_Db_Expr('NOW()');
			$link->save();
		} else {
			$row = $link_table->createRow ();
			$row->user_id = $user_id;
			$row->link_url = $values ['url'];
            $row->last_run = new Zend_Db_Expr('NOW()');
			$row->save();
		}

		$feeds = array_reverse ( $feed ['entries'] );

		// count blogs
		$count = $blog_number;
		foreach ( $feeds as $entry ) {
			$a = date ( 'Y-m-d', $entry ['pubDate'] );
			$pubdate = strtotime ( $a );
			// insert data to database
			$db = Engine_Api::_ ()->getItemTable ( 'blog' )->getAdapter ();
			$db->beginTransaction ();

			try {
				// check news exist by link
				$blog_table = Engine_Api::_ ()->getItemTable ( 'blog' );
				$blog_select = $blog_table->select ()->where ( 'link_detail = ?', $entry ['link_detail'] );
				$blog = $blog_table->fetchRow ( $blog_select );
				if ($blog) {
					$blog->title = $entry ['title'];
					$blog->pub_date = $pubdate;
					$blog->modified_date = date ( 'Y-m-d H:i:s' );
					if (! empty ( $entry ['content'] )) {
						$blog->body = $entry ['content'];
					} else {
						$blog->body = $entry ['description'];
					}
					$blog->is_approved = $is_approved;
					$blog->save ();
				} else {
					if ($max_blogs != 0 && $count >= $max_blogs) {
						continue;
					}
					$blog = $blog_table->createRow ();
					$blog->owner_type = "user";
					$blog->owner_id = $user_id;
					$blog->category_id = 0;
					$blog->creation_date = date ( 'Y-m-d H:i:s' );
					$blog->modified_date = date ( 'Y-m-d H:i:s' );
					$blog->pub_date = $pubdate;
					$blog->link_detail = $entry ['link_detail'];
					if (! empty ( $entry ['title'] )) {
						$blog->title = $entry ['title'];
					} else {
						$blog->title = 'Untitled Blog';
					}
					if (! empty ( $entry ['content'] )) {
						$blog->body = $entry ['content'];
					} else {
						$blog->body = $entry ['description'];
					}
					// trunglt - fix for blog-160
					$blog->is_approved = $is_approved;
					if ($blog->is_approved) {
						$blog->add_activity = 1;
					}

					$blog->save ();
					$count ++;

					// set auth
					$auth = Engine_Api::_ ()->authorization ()->context;
					$roles = array (
							'owner',
							'owner_member',
							'owner_member_member',
							'owner_network',
							'everyone'
					);
					$auth_view = "everyone";
					$auth_comment = "everyone";
					$viewMax = array_search ( $auth_view, $roles );
					$commentMax = array_search ( $auth_comment, $roles );
					foreach ( $roles as $i => $role )
						$auth->setAllowed ( $blog, $role, 'view', ($i <= $viewMax) );
					$auth->setAllowed ( $blog, $role, 'comment', ($i <= $commentMax) );

					if ($blog->is_approved) {
						$owner = $blog->getParent ();
						$action = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->addActivity ( $owner, $blog, 'ynblog_import' );

						// Make sure action exists before attaching the blog
						// to the activity
						if ($action) {
							Engine_Api::_ ()->getDbtable ( 'actions', 'activity' )->attachActivity ( $action, $blog );
						}

						// Send notifications for subscribers
						Engine_Api::_ ()->getDbtable ( 'subscriptions', 'ynblog' )->sendNotifications ( $blog );

					}
				}
				$db->commit ();
			} catch ( Exception $e ) {
				throw $e;
				$db->rollBack ();
			}
		}
		return 'completed';
	}

	/* ---- Private functions for import process ---- */
	function formatForSE($str) {
		$str = $this->formatVideoForSE ( $this->formatImageForSE ( $str ) );
		return $str;
	}
	function formatImageForSE($str) {
		if (preg_match_all ( '/(<p>)?\s*(<img[^>]*\/?>)\s*(<\/p>)?/', $str, $matches )) {
			for($i = 0; $i < sizeof ( $matches [0] ); $i ++) {
				$str = str_replace ( $matches [0] [$i], str_replace ( '/>', ' alt=""/>', $matches [2] [$i] ), $str );
			}
		}
		return $str;
	}
	function formatVideoForSE($str) {
		if (preg_match_all ( '/<object[\s\S]*src="([\S\s]*?)&amp;[\s\S]*"[\s\S]*<\/object>/', $str, $matches )) {
			for($i = 0; $i < sizeof ( $matches ); $i ++) {
				if ((strpos ( $matches [1] [$i], 'youtube.com' ) !== false)) {
					$str = str_replace ( $matches [0] [$i], '[youtube=' . $matches [1] [$i] . ']', $str );
				}
			}
		}
		return $str;
	}
}
