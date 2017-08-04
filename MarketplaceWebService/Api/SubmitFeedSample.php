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
*  @Date        01-08-2017
*  @Developerversion  version 1
*/
	//include mws credentials
	include_once ('.config.inc.php'); 
    if($_SERVER['REQUEST_METHOD']   ==  "POST")
    {
        $productFileType    =   pathinfo($_FILES['productupload']['name'],PATHINFO_EXTENSION);
        $supportfiletype    =   array('xml','xcd','xlsx','xlsm');
        //check file type
        $checkFileTyep      =   in_array($productFileType,$supportfiletype);
        if($checkFileTyep != true)
        {
            $result         =   array(
                                'Result'=>0,
                                'Message'=>'Invalid file format upload file like xml,xlsx'
                                );  
            echo json_encode([$result]);    
            return false;
        }
        //check file size 10 mb
        if ($_FILES["productupload"]["size"] > 10000000) 
        {
            $result         =   array(
                                'Result'=>0,
                                'Message'=>'File size should be below 10MB'
                                );  
            echo json_encode([$result]);    
            return false;
        }

    	$config 	= 	array 
    						(
    							'ServiceURL' => MWSSERVICEURL,
    							'ProxyHost' => null,
    							'ProxyPort' => -1,
    							'MaxErrorRetry' => 3,
    						);

    	$service 	= 	new MarketplaceWebService_Client
    						(
    							AWS_ACCESS_KEY_ID, 
    							AWS_SECRET_ACCESS_KEY, 
    							$config,
    							APPLICATION_NAME,
    							APPLICATION_VERSION
    						);

    // 	$feed 		= 	<<<EOD
    // 						<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amznenvelope.xsd">
    // 	    					<Header>
    // 	        					<DocumentVersion>1.01</DocumentVersion>
    // 	        					<MerchantIdentifier>MERCHANT_IDENTIFIER</MerchantIdentifier>
    // 	    					</Header>
    // 	    					<MessageType>Product</MessageType>
    // 	    					<PurgeAndReplace>false</PurgeAndReplace>
    // 	    					<Message>
    // 		        				<MessageID>1</MessageID>
    // 		        				<OperationType>Update</OperationType>
    // 		        				<Product>
    // 		            			<SKU>UNIQUE-TO-ME-1234</SKU>
    // 		            			<StandardProductID>
    // 		                		<Type>ASIN</Type>
    // 		                		<Value>B000A0S46M</Value>
    // 		            			</StandardProductID>
    // 		            			<Condition>
    // 		                			<ConditionType>New</ConditionType>
    // 		            			</Condition>
    // 		        				</Product>
    // 	    					</Message>
    // 						</AmazonEnvelope>
    // EOD;
        $filename   =   $_FILES["productupload"]["tmp_name"].'.'.$_FILES['productupload']['name'];
        // $row = 1;
        // if (($handle = fopen($filename, "r")) !== FALSE) 
        // {
        //     while (($data = fgetcsv($handle, 1000)) !== FALSE) 
        //     {
        //         if($row == 1){ $row++; continue; }
        //         $num    =   count($data);
        //         $result[]     =   $data;
               
        //     }
        //     fclose($handle);
        // }
		$feedHandle 	= 	@fopen('php://temp', 'rw+');
		fwrite($feedHandle, $filename);
		rewind($feedHandle);
		$marketplaceIdArray 	= 	array("Id" => array(MARKETPLACE_ID1));
		$request 		= 	new MarketplaceWebService_Model_SubmitFeedRequest();
		$request->setMerchant(MERCHANT_ID);
		$request->setMarketplaceIdList($marketplaceIdArray);
		$request->setFeedType('_POST_PRODUCT_DATA_');
		$con  = $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
		rewind($feedHandle);
		$request->setPurgeAndReplace(false);
		$request->setFeedContent($feedHandle);
		$request->setMWSAuthToken(MWSAUTHORISATIONTOKEN); // Optional
		rewind($feedHandle);
		invokeSubmitFeed($service, $request);
		@fclose($feedHandle);
    }
    else
    {
	   $result    =   array(
                            'Result'=>0,
                            'Message'=>'Invalid Access'
                            );   
        echo json_encode([$result]);
    }

	function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
  	{
      	try 
      	{
            $response 	= 	$service->submitFeed($request);
            if($response->isSetSubmitFeedResult()) 
            { 
                $submitFeedResult 	= 	$response->getSubmitFeedResult();
                if($submitFeedResult->isSetFeedSubmissionInfo()) 
                { 
                    $feedSubmissionInfo 	= 	$submitFeedResult->getFeedSubmissionInfo();
                    $result[] 	=	array('SubmitFeedResult'=>1,
                        			'FeedSubmissionInfo'=>$submitFeedResult->getFeedSubmissionInfo()
                        			);
                    if($feedSubmissionInfo->isSetFeedSubmissionId()) 
                    {
                    	$result[] =	array(
                        			'FeedSubmissionId'=>$feedSubmissionInfo->getFeedSubmissionId()
                        			);
                    }
                    if($feedSubmissionInfo->isSetFeedType()) 
                    {
                    	$result[] 	=	array(
                        				'FeedType'=>$feedSubmissionInfo->getFeedType()
                        				);
                    }
                    if($feedSubmissionInfo->isSetSubmittedDate()) 
                    {
                    	$result[] 	=	array(
                        				'SubmittedDate'=>$feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT)
                        				);
                    }
                    if($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                    {
                    	$result[] 	=	array(
                    					'FeedProcessingStatus'=>$feedSubmissionInfo->getFeedProcessingStatus()
                    				);
                    }
                    if($feedSubmissionInfo->isSetStartedProcessingDate()) 
                    {
                    	$result[] 	=	array(
                    					'StartedProcessingDate'=>$feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT)
                    				);
                    }
                    if($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                    {
                    	$result[] 	=	array(
                    					'CompletedProcessingDate'=>$feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT)
                    				);
                    }
                } 
            } 
            if ($response->isSetResponseMetadata()) 
            { 
            	$result[] 	=	array(
                    					'ResponseMetadata'=>$response->getResponseMetadata()
                    				);
                $responseMetadata 	= 	$response->getResponseMetadata();
                if ($responseMetadata->isSetRequestId()) 
                {
                	$result[] 	=	array(
                    					'RequestId'=>$responseMetadata->getRequestId()
                    				);
                }
            } 
            $result[] 	=	array(
                    			'ResponseHeaderMetadata'=>$response->getResponseHeaderMetadata()
                    		);
            echo json_encode($result);
     	} 
     	catch (MarketplaceWebService_Exception $ex) 
     	{

     		$result   	=	array(
      							'SubmitFeedResult'=>0,
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