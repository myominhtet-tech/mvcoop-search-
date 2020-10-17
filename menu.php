<nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="container-fluid">

            <button type="button" id="sidebarCollapse" class="btn btn-primary">
              <i class="fa fa-bars"></i>
              <span class="sr-only">Toggle Menu</span>
            </button>
            <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>
         
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <!-- search box -->
              <form class="form-inline my-2 my-lg-0 ml-auto" action="<?php echo URLROOT ?>/page/search" method="POST">
                <div class="input-group">
                  <input type="text" name="search" class="form-control" placeholder="Search" aria-label="Recipient's username" aria-describedby="button-addon2">
                  <div class="input-group-append">
                  <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
                  <i class="fas fa-search"></i>
                  </button>
                </div>
                </div>               
              </form>
              <!-- /search box -->
              <ul class="nav navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT ?>/income">Income</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>/expense">Expense</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URLROOT; ?>/auth/logout">Log Out</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>