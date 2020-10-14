<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Knjigovodstvo</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo $stylesheet ?>css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="<?php echo $stylesheet ?>css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Timeline CSS -->
    <link href="<?php echo $stylesheet ?>css/plugins/timeline.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $stylesheet ?>css/sb-admin-2.css" rel="stylesheet">
    <!-- Modifikacija CSS-a -->
    <link href="<?php echo $stylesheet ?>css/sb-admin-2_add.css" rel="stylesheet">
    
    
    <!-- Morris Charts CSS -->
    <link href="<?php echo $stylesheet ?>css/plugins/morris.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="<?php echo $stylesheet ?>font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <!-- jQuery -->
    <script src="<?php echo $stylesheet ?>js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo $stylesheet ?>js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo $stylesheet ?>js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="<?php echo $stylesheet ?>js/sb-admin-2.js"></script>
    
    <!-- Custom Theme JavaScript -->
    <script src="<?php echo $stylesheet ?>js/main.js"></script>
    
</head>
<body>
    
    <div id="wrapper">
    
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">ROLOSTIL szr v2.0</a>
        </div>
        <!-- /.navbar-header -->
    
        <ul class="nav navbar-top-links navbar-right">
            
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> Profil korisnika</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Podešavanja</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="../.inc/logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <?php include '../../app/includes/leftSidebarMeni.php';?>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>

        <div id="page-wrapper">
            
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">Knjigovodstvo</h3>
                </div>
            </div>
            <!--
            <div class="row">
                <div class="col-md-12">
                    
                    <a href="?inc=new" title="Upis novog klijenta u bazu!"><button type="button" class="btn btn-default btn-xs"><i class="fa fa-plus"> </i> Klijent</button></a>
                    
                </div>
            </div>
            <hr />
            -->
            <!-- /.row -->
            
            <div class="row">
                <div class="col-md-12">
                    <?php
                    if(isset($_GET['inc'])):
                        $inc = $_GET['inc'];
                        if($inc == "new") include '../../src/pidb/includes/formNew.php';
                        if($inc == "view") include '../../src/pidb/includes/formView.php';
                        if($inc == "viewInvoiceByClient") include '../../src/pidb/includes/viewInvoiceByClient.php';
                        if($inc == "listNotPayInvoices") include '../../src/pidb/includes/listNotPayInvoices.php';
                        if($inc == "totalIncome") include '../../src/pidb/includes/totalIncome.php';
                        
                        if($inc == "newExpense") include '../../src/pidb/includes/formNewExpense.php';
                        
                        if($inc == "edit") include '../../src/pidb/includes/formEdit.php';
                        if($inc == "search") include '../../app/includes/search.php';
                        if($inc == "alert") include '../../app/includes/alerts.php';
                    endif;
                    if(isset($_GET['set'])):
                        $settings = $_GET['set'];
                        include '../../src/pidb/includes/formSettings.php';
                    endif;
                    
                    if(!isset($_GET['inc']) AND !isset($_GET['set'])):
                        ?>
                        <a href="/pidb/index.php?inc=new"><button type="submit" class="btn btn-sm btn-default" title="Otvaranje novog dokumenta!"><i class="fa fa-plus"> </i> Dokument</button></a>
                        <p></p>
                        <?php
                        $documents_home = $pidb->getLastDocuments(10);
                        foreach ($documents_home as $key=>$pidbs):
                            $count = 0;
                            if($key == 1){ 
                                $vrsta = "Predračun";
                                $prefix = "P_";
                                $style = 'info';
                            }
                            if($key == 2){
                                $vrsta = "Otpremnica";
                                $prefix = "O_";
                                $style = 'default';
                            }
                            if($key == 3){
                                $vrsta = "Račun";
                                $prefix = "R_";
                                $style = 'success';
                            }
                            if($key == 4){
                                $vrsta = "Povratnica";
                                $prefix = "";
                                $style = 'warning';
                            }
                            ?>
                            <div class="panel panel-<?php echo $style; ?>">
                                <div class="panel-heading"><?php echo $vrsta;?></div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="50">#</th>
                                                    <th width="150"><?php echo $vrsta; ?></th>
                                                    <th>Naziv klijenta</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // prvo izlistavamo dokumente koji nisu arhivirani
                                                foreach ($pidbs as $pidb):
                                                    if ($pidb['archived'] == 0):
                                                        $count++;
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $count ?></td>  
                                                            <td><a href="?inc=view&pidb_id=<?php echo $pidb['id']; ?>&pidb_tip_id=<?php echo $pidb['tip_id']; ?>"><?php echo $prefix . str_pad($pidb['y_id'], 4, "0", STR_PAD_LEFT) . ' - ' . date('m / Y', strtotime($pidb['date'])); ?></a></td>
                                                            <td><?php echo ( $key == 3 ? '<a href="?inc=viewInvoiceByClient&client_id=' .$pidb['client_id']. '">' . $pidb['client_name'] .'</a>' : $pidb['client_name'] ) ; ?></td>
                                                            <td><?php echo $pidb['title']; ?></td>
                                                        </tr>
                                                        <?php
                                                    endif;
                                                endforeach;
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->
                                </div>
                                <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                    
                        <?php
                        endforeach;
                        
                        
                    endif;
                    
                    
                    
                    ?>    
                </div>
            </div>
            <!-- /.row -->
            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    
    
    <!-- Morris Charts JavaScript 
    <script src="<?php echo $stylesheet ?>js/plugins/morris/raphael.min.js"></script>
    <script src="<?php echo $stylesheet ?>js/plugins/morris/morris.min.js"></script>
    <script src="<?php echo $stylesheet ?>js/plugins/morris/morris-data.js"></script>
    -->
    

</body>
</html>
