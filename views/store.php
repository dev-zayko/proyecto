<?php
include('header.php');
?>
<div class="container-fluid bg-3 text-center" id="div_product">
  <h3 id="heading_store">Bodega</h3>
  <button type="button" class="btn btn-primary pull-right" id="button_add_store"><i class="fa-solid fa-circle-plus"></i> Añadir Bodega</button>
  <br>
  <div class="panel panel-primary">
    <div class="panel-body table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr class="active">
            <th>#</th>
            <th>Cod Bodega</th>
            <th>Creada el</th>
            <th>Disponible</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody id="store_table">
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_store" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <div class="container">
          <div class="row">
            <div class="col-sm">
              <h5 class="modal-title" id="title_store"></h5>
            </div>
            <div class="col-sm">
              <button type="submit" id="button_available" class="btn btn-warning">Inhabilitar Bodega</button>
            </div>
          </div>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="panel panel-primary">
          <div class="panel-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr class="active">
                  <th>#</th>
                  <th>Cod Producto</th>
                  <th>Nombre</th>
                  <th>Precio</th>
                  <th>Stock</th>
                </tr>
              </thead>
              <tbody id="product_store_table">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>