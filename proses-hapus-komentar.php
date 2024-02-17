<?php

   include 'db.php';
      
   if(isset($_GET['idp'])){
		$id = mysqli_query($conn, "SELECT image_id FROM komentar_foto WHERE komentarID = '".$_GET['idp']."' ");
		$p = mysqli_fetch_object($id);
		
		// unlink('./foto/'.$p->image);

		// mysqli_query($conn, "DELETE FROM tb_like WHERE image_id = '".$_GET['idp']."' ");
		
		mysqli_query($conn, "DELETE FROM komentar_foto WHERE komentarID = '".$_GET['idp']."' ");
	   
		// mysqli_query($conn, "DELETE FROM tb_image WHERE image_id = '".$_GET['idp']."' ");
		echo "<script>window.location='detail-image-dashboard.php?id=$p->image_id'</script>";


   }

?>