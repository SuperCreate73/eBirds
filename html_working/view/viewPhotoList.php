<?php
	$title = 'Mon nichoir: Photos et vidéos';
	$styles = '<link href="public/css/stylePhotoList.css" rel="stylesheet" type="text/css" />';
?>

<?php ob_start(); ?>

	<script src="vendor/jQuery/jquery-3.3.1.min.js"></script>
	<script src="public/js/fileUtility.js"></script>

<?php $javaScripts = ob_get_clean(); ?>

<?php ob_start(); ?>	<!-- Contenu de la page, intégré à la variable $content -->

	<div class="menuPhoto">
		<div class="photoNav">
			<div class="navCommands">
				<a href="index.php?page=photoList&amp;param1=first">First</a>
				<a href="index.php?page=photoList&amp;param1=previous">Previous</a>
				<a href="index.php?page=photoList&amp;param1=next">Next</a>
				<a href="index.php?page=photoList&amp;param1=last">Last</a>
			</div>
		</div>
		<div class="pageCount">Page <?= $page ?> sur <?= $numberOfPage ?> </div>
		<div class="menuEdit">
				<div id="btn_comment" class='my_button' value="comment" title="Comment selected files" onclick="commentFiles()"></div>
				<div id="btn_tag" class="my_button" value="tag" title="Tag selected files" onclick="tagFiles()"></div>
				<div id="btn_download" class="my_button" value="download" title="Download selected files" onclick="downloadFiles()"></div>
				<div id="btn_toggleSelect" class="my_button toggleUnSelect" value="mode Select" title="Activer le mode sélection" onclick="toggleSelectMode()" ondblclick="selectAll()"></div>
				<div id="btn_delete" class="my_button" value="delete" title="Delete selected files" onclick="deleteFiles()"></div>
		</div>
	</div>

	<form id="hiddenForm" class="hiddenForm" name="hiddenForm" method="post">
		<input type="hidden" name="valueTable">
	</form>
	<div id="explorerPane" class="explorerPane" >
		<div id="photoThumbContainer" class="photoUnselect">
			<div id="photoThumb" class="photoThumb" onclick="showPhotoSelection()"></div>
			<div id="photoInfo" class="photoInfo">
				<div id="fileName" class="fileName" value="Nom :">name</div>
				<div id="fileTag" class="fileTag" value="Tag :">tag</div>
				<div id="nbrSelected" class="nbrSelected" value="Photos sélectionnées : ">photos </div>

			</div>
		</div>

		<span id="photoThumbContainer2" class="photoHide">
			<div id="photoThumb2" class="photoThumb"></div>
			<div id="photoInfo2" class="photoInfo">
				<div id="fileName2" class="fileName" value="Nom :">name</div>
				<div id="fileTag2" class="fileTag" value="Tag :">tag</div>
				<div id="nbrSelected2" class="nbrSelected" value="Photos sélectionnées : ">photos </div>

			</div>
		</span>

		<div id="photoList" class="photoList">

		<?php
			$count=0;
			foreach ($explorerPane as $explorerPaneCol) {
				$count+=1;
		?>

			<div id="explorer<?= $count ?>" class="listPane" ondblclick="invertSelection(this.id)">

			<?php
				foreach ($explorerPaneCol as $item) {
					$pathParts = pathinfo($fileManager->filePath().$item);
			?>

				<div class="fileList" extension="<?= $pathParts["extension"] ?>" onclick="selectFiles(this)" id="<?= $pathParts["filename"] ?>">
					<div class="fileExtension <?= $pathParts["extension"] ?>"></div>
					<div class="fileItem"><?= $pathParts["filename"] ?></div>
					<div class="fileTag tag"></div>
					<div class="fileComment comment"></div>
				</div>
			<?php } ?>

			</div>
		<?php } ?>
	   	</div>
   	</div>
<?php $content = ob_get_clean(); ?>

<?php require('view/template.php'); ?>
