<?php
//include APPLICATION_PATH . '/application/modules/Socialstore/cli.php'; 


define('DEBUG', true);

ini_set('max_execution_time', 3000);

if(DEBUG) {
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
	ini_set('error_reporting', -1);
}else{
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	ini_set('error_reporting', E_STRICT); 
	
}

define('_ENGINE_CUR_PATH', dirname(__FILE__));
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));

// Config
if(!defined('_ENGINE_R_MAIN')) {
	define('_ENGINE_R_REWRITE', true);
	define('_ENGINE_R_CONF', true);
	define('_ENGINE_R_INIT', true);
	$indexFile = APPLICATION_PATH . '/application/index.php';
	//exit($indexFile);

	include_once $indexFile;
}

// Create application, bootstrap, and run
$application = Engine_Api::getInstance()->getApplication();

//$application = Engine_Api::getInstance()->getApplication();

$application -> getBootstrap() -> bootstrap('frontcontroller');
$application -> getBootstrap() -> bootstrap('cache');
$application -> getBootstrap() -> bootstrap('db');
$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('frontcontrollermodules');
$application -> getBootstrap() -> bootstrap('session');
$application -> getBootstrap() -> bootstrap('manifest');
$application -> getBootstrap() -> bootstrap('router');
$application -> getBootstrap() -> bootstrap('view');
$application -> getBootstrap() -> bootstrap('layout');
$application -> getBootstrap() -> bootstrap('modules');

function getLog($filename='store.notify.log'){
		$writer =  new Zend_Log_Writer_Stream(APPLICATION_PATH .'/temporary/log/'.$filename);
		return new Zend_Log($writer);
}



require_once APPLICATION_PATH . '/application/modules/Ynevent/externals/scripts/library/googlemerchantcalculations.php';
require_once APPLICATION_PATH . '/application/modules/Ynevent/externals/scripts/library/googleresult.php';
require_once APPLICATION_PATH . '/application/modules/Ynevent/externals/scripts/library/googlerequest.php';
require_once APPLICATION_PATH . '/application/modules/Ynevent/externals/scripts/library/googleresponse.php';
$Gresponse = new GoogleResponse();
$xml_response = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
if (get_magic_quotes_gpc()) {
    $xml_response = stripslashes($xml_response);
}
list ($root, $data) = $Gresponse->GetParsedXML($xml_response);
$response = $data;
	


switch ($root) {
  case "request-received": {
      //process_request_received_response($Gresponse);
      getLog('store.response.log')->log(print_r('1',true), Zend_Log::DEBUG);
      break;
    }
  case "error": {
      //process_error_response($Gresponse);
      getLog('store.response.log')->log(print_r('2',true), Zend_Log::DEBUG);
      break;
    }
  case "diagnosis": {
      getLog('store.response.log')->log(print_r('3',true), Zend_Log::DEBUG);
      //process_diagnosis_response($Gresponse);
      break;
    }
  case "checkout-redirect": {
  	 getLog('store.response.log')->log(print_r('4',true), Zend_Log::DEBUG);
      //process_checkout_redirect($Gresponse);
      break;
    }
  case "merchant-calculation-callback" :
    {
//      if (MODULE_PAYMENT_GOOGLECHECKOUT_MULTISOCKET == 'True') {
//        include_once ($curr_dir . '/googlecheckout/multisocket.php');
//        process_merchant_calculation_callback($Gresponse, 2.7, false);
//        break;
//      }
//    }
//  case "merchant-calculation-callback-single" :
//    {
      // 			set_time_limit(5); 
      //process_merchant_calculation_callback_single($Gresponse);
       getLog('store.response.log')->log(print_r('5',true), Zend_Log::DEBUG);
      break;
    }
  case "new-order-notification" :
    {
 		getLog('store.response.log')->log(print_r('6',true), Zend_Log::DEBUG);
      	process_new_order_notification($response);
 		break;
    }
  case "order-state-change-notification": {
  	 getLog('store.response.log')->log(print_r('7',true), Zend_Log::DEBUG);
      process_order_state_change_notification($response);
      break;
    }
  case "charge-amount-notification": {
  	 getLog('store.response.log')->log(print_r('8',true), Zend_Log::DEBUG);
      process_charge_amount_notification($response);
      break;
    }
  case "chargeback-amount-notification": {
  	 getLog('store.response.log')->log(print_r('9',true), Zend_Log::DEBUG);
     // process_chargeback_amount_notification($Gresponse);
      break;
    }
  case "refund-amount-notification": {
  	 getLog('store.response.log')->log(print_r('19',true), Zend_Log::DEBUG);
  //process_refund_amount_notification($Gresponse, $googlepayment);
      break;
    }
  case "risk-information-notification": {
  	 getLog('store.response.log')->log(print_r('11',true), Zend_Log::DEBUG);
      process_risk_information_notification($response);
      break;
    }
  default: {
  	 getLog('store.response.log')->log(print_r('12',true), Zend_Log::DEBUG);
      //$Gresponse->SendBadRequestStatus("Invalid or not supported Message");
      break;
    }
}
exit (0);

function process_new_order_notification($response) {
	$transaction_id = $response['new-order-notification']['google-order-number']['VALUE'];
	$params = array();
	$params = $response['new-order-notification']['shopping-cart']['merchant-private-data'];
	  foreach ($params as $key => $value) {
			$params[$key] = $value['VALUE'];
	  }
	$params['transaction_id'] = $transaction_id;
	$params['amount'] = $params['total_amount'];
	$params['gateway'] = 'google';
	Socialstore_Api_Transaction::getInstance()->addTransaction($params);
	//getLog('store.response.log')->log(print_r($response,true), Zend_Log::DEBUG);

	/*$select = $Trans->select()->from($Trans->info('name'));
	$google_order = $db->Execute("select orders_id ".
                                " from " . $googlepayment->table_order . " " .
                                " where google_order_number = " . 
                                $data[$root]['google-order-number']['VALUE'] );
    if($google_order->RecordCount() != 0) {
//  Order already processed, send ACK http 200 to avoid notification resend
    	$Gresponse->log->logError(sprintf(GOOGLECHECKOUT_ERR_DUPLICATED_ORDER,
                                   $data[$root]['google-order-number']['VALUE'],
                                   $google_order->fields['orders_id']));
        $Gresponse->SendAck(); 
      }*/
}
function process_order_state_change_notification($response) {
	$new_financial_state = $response['order-state-change-notification']['new-financial-order-state']['VALUE'];
  	$new_fulfillment_order = $response['order-state-change-notification']['new-fulfillment-order-state']['VALUE'];

  	$previous_financial_state = $response['order-state-change-notification']['previous-financial-order-state']['VALUE'];
  	$previous_fulfillment_order = $response['order-state-change-notification']['previous-fulfillment-order-state']['VALUE'];
	if ($previous_financial_state != $new_financial_state) {
	    $transaction_id = $response['order-state-change-notification']['google-order-number']['VALUE'];
	    $transaction =  Socialstore_Model_DbTable_PayTrans::getByTransId($transaction_id,'google');
		$order = Socialstore_Model_DbTable_Orders::getByOrderId($transaction->order_id);
		$plugin = $order->getPlugin();
		
	    switch ($new_financial_state) {
	      case 'REVIEWING' :
	        {
	        	break;
	        }
	      case 'CHARGEABLE' :
	        {
	        	break;
	        }
	      case 'CHARGING' :
	        {
	        	break;
	        }
	      case 'CHARGED' :
	        {
	        	$transaction -> payment_status = 'Completed';
	        	$transaction -> save();
				$plugin->onSuccess();
	        	break;
	        }
	
	      case 'PAYMENT-DECLINED' :
	        {
	        	break;
	        }
	      case 'CANCELLED' :
	        {
	        	$plugin->onCancel();
	        	break;
	        }
	      case 'CANCELLED_BY_GOOGLE' :
	        {
	        	$plugin->onCancel();
	        	break;
	        }
	      default :
	      	    break;
	    }
	 }  
		
}
function process_charge_amount_notification($response) {
	getLog('store.response.log')->log(print_r($response,true), Zend_Log::DEBUG);
}
function process_risk_information_notification($response) {
	getLog('store.response.log')->log(print_r($response,true), Zend_Log::DEBUG);
}
?> 