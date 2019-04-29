<?php 

require_once 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

 # Mengatur instance dari Azure::Storage::Client
$connectionString = "DefaultEndpointsProtocol=https;AccountName=sub2dicoding;AccountKey=zdoVz7lOjiAxMIOxK1Y2rgWVrUTlMVbMCFW2LfV6RSq4kHtfqJwzMBkZCLyJpzYbIQGz7h2UkXz36x+g5kBjDw==;EndpointSuffix=core.windows.net";
    
$containerName = "blockdicoding";

$blobClient = BlobRestProxy::createBlobService($connectionString);
    
if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["myfile"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	// echo fread($content, filesize($fileToUpload));

	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: index.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");

$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Submission 2 Analyze Image</title>
</head>
<body>
	<h1>Analyze image:</h1>
	Enter the URL to an image, then click the <strong>Analyze image</strong> button.
	<br><br>
	<form action="index.php" method="post">
		<input type="file" name="myfile" accept=".jpeg,.jpg,.png">	
		<input type="submit"  name="submit" value="Upload">
	</form>	
	<br>
	<h3>Total Files: <?php echo sizeof($result->getBlobs()) ?></h3>
	<table border="3">
		<thead>
			<tr>
				<th>Nama File</th>
				<th>Url File</th>
				<th></th>
			</tr>
		</thead>
		<tbody>			
			<?php 
				do{					    
				    foreach ($result->getBlobs() as $oneblob) {					    						   
			?>
				<tr>
					<td><?php echo $oneblob->getName() ?></td> 
					<td><?php echo $oneblob->getUrl() ?></td> 
					<td>
						<form action="cognitive_service.php" method="post">
							<input type="hidden" name="url" value="<?php echo $oneblob->getUrl()?>">
							<input type="submit" name="submit" value="Analyze">
						</form>
					</td> 
				</tr>				
			<?php 
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
			?>			
		</tbody>		
	</table>
</body>
</html>
