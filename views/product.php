<?php
include('header.php');
?>

<div class="container-fluid text-center" id="div_product">
  <h3 id="heading_product">Productos</h3>
  <button type="button" class="btn btn-primary pull-right" id="button_add_product"><i class="fa-solid fa-circle-plus"></i> Añadir Producto</button>
  <br>
  <div class="panel panel-primary">
    <div class="panel-body table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr class="active">
            <th>#</th>
            <th>Cod Producto</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Creado el</th>
            <th>Bogeda</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody id="product_table">
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal para añadir/editar un Producto -->
<div class="modal fade" id="modal_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="form_product">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="title_modal"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name_product">Nombre</label>
            <input type="text" class="form-control" id="name_product" required />
            <input type="hidden" id="action_product" />
          </div>
          <div class="form-group">
            <label for="price_product">Precio</label>
            <input type="text" inputmode="numeric" required oninput="this.value = this.value.replace(/\D+/g, '')" class="form-control" id="price_product" />
          </div>
          <div class="form-group">
            <label for="stock_product">Stock Producto</label>
            <input type="text" inputmode="numeric" required class="form-control" oninput="this.value = this.value.replace(/\D+/g, '')" id="stock_product" />
          </div>
          <div class="form-group">
            <label for="store_product" id="lb_store"></label>
            <select class="form-control" id="store_product">
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" id="button_save">Guardar cambios</button>
        </div>
      </div>
    </form>
  </div>
</div>