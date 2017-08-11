<?php
/** 
*  PHP Version 5
*
*  @package     MarketplaceWebService
*  @copyright   Copyright Falconnect Technologies.
*  @link        http://www.falconnect.in/
*  @license     http://www.falconnect.in/
*  @version     2009-01-01
*  @Developer   vinoth 
*  @Date        11-08-2017
*  @Developerversion  version 1
*/
	//include mws credentials
	include_once ('.config.inc.php'); 
	if(isset($_POST['inventorytype']) && $_SERVER['REQUEST_METHOD'] 	==	"POST")
	{
	    $inventorytype   = 	$_POST['inventorytype'];
	    if($inventorytype 	==	"")
	    {
	    	$result   	=	array(
      							'Result'=>0,
								'Message'=>'Inventorytype is required'
								);  
			echo json_encode([$result]);    
			return false;
	    }
		$config 	= 	array (
								'ServiceURL' => MWSSERVICEURL,
								'ProxyHost' => null,
								'ProxyPort' => -1,
								'MaxErrorRetry' => 3,
								);
		$service 	= 	new MarketplaceWebService_Client(
												AWS_ACCESS_KEY_ID, 
												AWS_SECRET_ACCESS_KEY, 
												$config,
												APPLICATION_NAME,
												APPLICATION_VERSION
												);
		$marketplaceIdArray = 	array("Id" => array(MARKETPLACE_ID1));
	 
		$parameters = 	array (
								'Merchant' => MERCHANT_ID,
								'MarketplaceIdList' => $marketplaceIdArray,
								'ReportType' => '_GET_MERCHANT_LISTINGS_DATA_',
								'ReportOptions' => 'ShowSalesChannel=true',
								'MWSAuthToken' => MWSAUTHORISATIONTOKEN
								);
		$request 	= 	new MarketplaceWebService_Model_RequestReportRequest($parameters);
 		invokeRequestReport($service, $request);
 	}
	else
	{
		$result   	=	array(
	  							'Result'=>0,
								'Message'=>'Invalid Access'
								);   
		echo json_encode([$result]);
	}

	function invokeRequestReport(MarketplaceWebService_Interface $service, $request) 
  	{
      	try 
      	{
        	$response 	= 	$service->requestReport($request);
            if ($response->isSetRequestReportResult()) 
            { 
                $requestReportResult 	= 	$response->getRequestReportResult();
                $result[] 	=	array('ReportResult'=>1,
                					'RequestReportResult'=>$requestReportResult
                					);
                if($requestReportResult->isSetReportRequestInfo()) 
                {
                    
                    $reportRequestInfo 	= 	$requestReportResult->getReportRequestInfo();
                  	if ($reportRequestInfo->isSetReportRequestId()) 
                  	{
                      	$result[] 	=	array('ReportRequestId'=>
                      							$reportRequestInfo->getReportRequestId()
                							);
                   	}
                    if ($reportRequestInfo->isSetReportType()) 
                    {
                    	$result[] 	=	array('ReportType'=>
                      							$reportRequestInfo->getReportType()
                							);
                    }
                    if ($reportRequestInfo->isSetStartDate()) 
                    {
                    	$result[] 	=	array('StartDate'=>
                      					$reportRequestInfo->getStartDate()->format(DATE_FORMAT)
                							);
                    }
                    if ($reportRequestInfo->isSetEndDate()) 
                    {
                    	$result[] 	=	array('EndDate'=>
                      					$reportRequestInfo->getEndDate()->format(DATE_FORMAT)
                							);
                    }
					if ($reportRequestInfo->isSetSubmittedDate()) 
					{
						$result[] 	=	array('SubmittedDate'=>
                      					$reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT)
                							);
					}
					if ($reportRequestInfo->isSetReportProcessingStatus()) 
					{
						$result[] 	=	array('ReportProcessingStatus'=>
                      							$reportRequestInfo->getReportProcessingStatus()
                							);
					}
                }
            } 
            if ($response->isSetResponseMetadata()) 
            { 
                $responseMetadata 	= 	$response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) 
                {
                	$result[] 	=	array('ResponseMetadata'=>$responseMetadata,
                    							'RequestId'=>$responseMetadata->getRequestId()
                    							);
                }
            } 
            $result[] 	=	array('ResponseHeaderMetadata'=>
            							$response->getResponseHeaderMetadata()
            						);
            echo json_encode($result);
     	} 	
     	catch (MarketplaceWebService_Exception $ex) 
     	{
        	$result   	=	array(
      							'ReportResult'=>0,
								'Caught_Exception'=>$ex->getMessage(),
								'Response_Status_Code'=>$ex->getStatusCode(),
								'Error_Code'=>$ex->getErrorCode(),
								'Error_Type'=>$ex->getErrorType(),
								'Request_ID'=>$ex->getRequestId(),
								'XML'=>$ex->getXML(),
								'ResponseHeaderMetadata'=>$ex->getResponseHeaderMetadata()
								);   
			echo json_encode([$result]); 
     	}
 	}
?>