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
</STYLE>

	

<div id="BodyContent" class="widget">

	<div class="col-md-6">
	<?php echo $viewContent;?>		
	</div>
	<div class="col-md-6">
		<div id="Preview" class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Preview</h3>
                  
                  <div class="pull-right box-tools">                   
                 	<button class="btn btn-box-tool" data-click="box-chain" >
						<i class="fa fa-chain" data-toggle="tooltip" data-original-title="轉繁體"></i>					
					</button>
					<button class="btn btn-box-tool" data-click="box-save" >
						<i class="fa fa-save" data-toggle="tooltip" data-original-title="save"></i>					
					</button>
                  </div>
                </div><!-- /.box-header -->
                  <div class="box-body">
                  <div class="form-group">
                      <label for="inputEmail3" class="col-sm-2 control-label">Start Time</label>
                      <div class="col-sm-10">
                        <input preview="start_time" data-type="value" type="number" class="form-control" placeholder="Start Time" value="0">
                      </div>
                    </div>
                  <div class="form-group">
                      <label>Title</label>                      
                      <textarea preview="title" data-type="html" class="form-control" rows="3" placeholder="Enter ..." onchange="$(this).html(this.value);"></textarea>
                  </div>
                  <div class="form-group">
                      <label>Memo</label>
                      <textarea preview="memo" data-type="html" class="form-control" rows="3" placeholder="Enter ..." onchange="$(this).html(this.value);"></textarea>
                  </div>  
                  
                  </div>
                  <div id="preview_html" class="box-footer">
                    
                  </div>
                
              </div>
	</div>
<script>
			
			$('#Preview button[data-click="box-save"]').on('click',function(){

				var preview = {}; 
				$('#Preview [preview]').each(function(){				

					if($(this).attr('data-type') == 'value') preview[$(this).attr("preview")] = $(this).val();		
					else preview[$(this).attr("preview")] = $(this).html();
				});
				
				var id = $('#Preview').attr('data-id');
				var url = 'video/update';
				var info = {
						id : id,
						data : 'video',
						preview : preview
					};
				console.info(info);
				$.ajax({
					url: url,
				  cache: false,
					dataType: 'html',
				  type: 'POST',
				  data:{
					  info:info
				  },
				  success: function(data){	
					  console.info(data);
					  $('#preview_html').html('');
					  $('#Preview [preview]').html('');
					  boxRefresh($('#Video'));
				  }
				});
				
			});

			
			$('#Preview button[data-click="box-chain"]').on('click',function(){
				$('#Preview [preview]').each(function(){		
					$(this).html(Traditionalized($(this).html()));
				});	
			})
			</script>

	
	
</div>



