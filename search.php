
<?php require APPROOT . '/views/components/header.php';?>

<!-- url jumping protect -->
<?php
if(isset($_SESSION["email-session"])) {
    //  echo "User login is :" . $_SESSION["email-session"];
    // exit;
}
else {
  echo "no permission";
  exit;
}
?>
<!-- url jumping protect -->

<div class="wrapper d-flex align-items-stretch">

	   <?php include(APPROOT.'/views/components/sidebar.php'); ?>

      <!-- Page Content  -->
      <div id="content" class="p-4 p-md-5">

      <?php include(APPROOT.'/views/components/menu.php'); ?>

      <h2 class="mb-4">Search Result</h2>
        <span><?php echo date("jS \of F Y ( l )"); ?></span>

       
              <?php  
              if (!$data['searchResult']) {
                 echo "
                    <div class='card'>
                        <div class='card-body'>
                        There is no result for this.
                        </div>
                    </div>
                 ";
                 exit;
              }
              else {
               foreach( $data['searchResult'] as $search )
            { 
                 ?>
            
            <div class="card my-1">
                <div class="card-body">
                    <a class="search-link" href="
                    <?php 
                        if ($search['type_name'] == 'income') {
                            echo URLROOT. '/income';
                        } else {
                            echo URLROOT.'/expense';
                        }
                    ?>
                    " style="color: #005cbf!important;">
                        <p class="text-reset"><?php echo "<strong class='text-uppercase'>". $search['type_name'] ."</strong> | ". $search['category_name']; ?></p>
                    </a>
                    <div><?php echo $search['description']; ?> </div>
                </div>
            </div>
            
                <?php }
             } ?>
                
             

          

            <!-- <div class="card border-info my-3 mx-4" style="max-width: 18rem;">
            <div class="card-header">Today Transitions</div>
              <div class="card-body">
                <h5 class="card-title">Primary card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
              </div>
            </div> -->
            
        
      </div>
		</div>


<?php require APPROOT . '/views/components/footer.php';?>
