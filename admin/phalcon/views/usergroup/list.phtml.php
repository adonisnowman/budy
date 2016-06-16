<STYLE>
TR[data-onshelf="onshelf"] BUTTON[data-click="item-onshelf"] {
	color: #00a65a;
}

TR[data-onshelf="offshelf"] BUTTON[data-click="item-offshelf"] {
	color: red;
}

.box.box-solid.box-success>.box-header  UL.dropdown-menu li a {
	color: #777;
}
</style>
<div class="widget">
	
	<div class="col-md-3">
	<?php echo $viewContent;?>
		<div id="account_Box"></div>
	</div>
	<div class="col-md-9">
		<div id="purviewlist_Box"></div>
	</div>
</div>


