// Theme add: Call the dataTables jQuery plugin
$(document).ready(function() {
  $('table.dataTable').dataTable();
});


// wait for load all document
$(document).ready(function(){
    
  $('#selectTip').change(function(){
      
      // preuzimamo value elementa koji se nadgleda, u ovom slucaju je select 
      // sa id vps
      var vps_id = $(this).val();
      
      // definisemo sadrzaj promenljive pib
      var pib = `
                <div class="form-group row">
                  <label for="pib" class="col-sm-3 col-lg-2 col-form-label text-right">PIB: </label>
                  <div class="col-sm-3">
                    <input class="form-control" type="text" id="pib" name="pib" value=""  maxlength="9" />
                  </div>
                </div>
                `;
      
      // ako je fizicko lice div id="pib" se puni sa html('')
      if(vps_id==='1') $('div#pib').html('');;
      
      // ako je pravno lice div id="pib" se puni sa html(pib)
      if(vps_id==='2') $('div#pib').html(pib);;
      
  });
  
});

