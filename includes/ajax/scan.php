<html>
	<head>
		<title>Image Scanner Application</title>
	</head>

	<body>
		<p>This is a simple test page for the DynamicWebTWAIN plug-in.</p>
		<EMBED
			TYPE="Application/DynamicWebTwain-Plugin"
			OnPreTransfer="OnPreTransferCallback"
			OnPostTransfer="OnPostTransferCallback"
			OnPreAllTransfers="OnPreAllTransfersCallback"
			OnPostAllTransfers="OnPostAllTransfersCallback"
			OnTransferCancelled="OnTransferCancelledCallback"
			OnTransferError="OnTransferErrorCallback"
			OnMouseClick="OnMouseClickCallback"
			OnMouseMove="OnMouseMoveCallback"
			OnMouseRightClick="OnMouseRightClickCallback"
			OnMouseDoubleClick="OnMouseDoubleClickCallback"
			OnTopImageInTheViewChanged="OnTopImageInTheViewChangedCallback"
			OnImageAreaSelected="OnImageAreaSelectedCallback"
			OnImageAreaDeSelected="OnImageAreaDeSelectedCallback"
			
			WIDTH="100"
			HEIGHT="100"
			PLUGINSPAGE="http://<?=$_SERVER['SERVER_NAME']?>/plugins/DynamicWebTWAINPlugIn.exe">
		</EMBED>

		<input type="button" onClick="AcquireImage()" value="AcquireImage" >
		<script>
		function OnPreTransferCallback(){
			// Add your code here
		}
		function OnPostTransferCallback(){
			// Add your code here
		}
		function OnPreAllTransfersCallback(){
			// Add your code here
		}
		function OnPostAllTransfersCallback(){
			// Add your code here
		}
		function OnTransferCancelledCallback(){
			// Add your code here
		}
		function OnTransferErrorCallback(){
			// Add your code here
		}
		function OnMouseClickCallback(sImageIndex){
			// Add your code here
		}
		function OnMouseMoveCallback(sImageIndex){
			// Add your code here
		}
		function OnMouseRightClickCallback(sImageIndex){
			// Add your code here
		}
		function OnMouseDoubleClickCallback(sImageIndex){
			// Add your code here
		}
		function OnTopImageInTheViewChangedCallback(sImageIndex){
			// Add your code here
		}
		function OnImageAreaSelectedCallback(sImageIndex, left, top, right, bottom){
			// Add your code here
		}
		function OnImageAreaDeSelectedCallback(sImageIndex){
			// Add your code here
		}
		function AcquireImage(){
			// scan image
			var plugin = document.embeds[0];
			plugin.AcquireImage();
		}
		</script>
	</body>
</html>