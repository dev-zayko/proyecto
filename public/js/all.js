$(document).ready(async () => {
    var table_product = document.getElementById('product_table');
    var table_store = document.getElementById('store_table');

    if (table_product != null) {
        var stores = await get_products();
        add_product(stores);
        edit_product();
    }
    if (table_store != null) {
        get_stores();
        add_store();
        edit_store();
    }
});

//#region FUNCIONES CRUD

//#region FUNCIONES CRUD BODEGA

/**
 * Obtiene el elemento de la tabla del DOM, lo borra y luego realiza una llamada ajax a un StoreController.php, que
 * devuelve un objeto json, que luego se itera y cada objeto se pasa a una función que
 * crea una fila en la tabla.
 */
const get_stores = () => {
    const $store_table = document.getElementById('store_table');
    $store_table.innerHTML = "";
    $.ajax({
        type: "GET",
        url: "/prueba/controllers/StoreController.php?action=get",
        dataType: "json",
    }).done((response) => {
        $.each((response), (key, store) => {
            charge_table_store($store_table, store, key);
        });
    }).fail((jqXHR, textStatus, errorThrown) => {
        alert_error("Error al obtener las bodegas", errorThrown);
    });
}

/**
 * Se agrega un detector de eventos a un botón, y cuando se hace clic en el botón, llama a una función que
 * muestra un cuadro de diálogo de confirmación, y si el usuario confirma, envía una solicitud AJAX a un StoreController.php,
 * y si la llamada regresa con éxito, llama a  un alert confirmando el guardado exitoso
 */
const add_store = () => {
    const $button_add = document.getElementById("button_add_store");
    $button_add.addEventListener('click', async () => {
        await alert_confirm().then((value) => {
            if (value == true) {
                $.ajax({
                    type: "POST",
                    url: "/prueba/controllers/StoreController.php",
                    data: {
                        action: 'add'
                    }
                }).done(() => {
                    get_stores();
                    alert_success('Entrada guardada');
                }).fail((jqXHR, textStatus, errorThrown) => {
                    alert_error("Error al eliminar", errorThrown);
                });
            }
        });
    });
}


/**
 * Primero se captura un boton que es el boton para habilitar o deshabilitar una bodega
 * despues se agrega un detector de eventos a un botón, y cuando se hace clic en el botón, envía una solicitud AJAX a StoreController.php,
 * y si la llamada regresa con éxito, llama a  un alert confirmando si se habilito o deshabilito la bodega
 */
const edit_store = () => {
    const $button_available = document.getElementById('button_available');
    $button_available.addEventListener('click', () => {
        const $available = $button_available.innerText;
        $.ajax({
            type: "POST",
            url: "/prueba/controllers/StoreController.php",
            data: {
                action: "edit",
                store: {
                    id: $button_available.value,
                    available: $available == 'Inhabilitar Bodega' ? false : true,
                }
            }
        }).done(() => {
            get_stores();
            $('#modal_store').modal('hide');
            alert_success($available == 'Inhabilitar Bodega' ? 'Deshabilitada' : 'Habilitada');
        }).fail((jqXHR, textStatus, errorThrown) => {
            alert_error("Error al eliminar", textStatus)
        });
    });
}


/**
 * Envía una solicitud AJAX a StoreController.php, que elimina logicamente una Bodega de la base de datos.
 * @param id_store - El id de la Bodega a ser eliminada
 */
const delete_store = (id_store) => {
    $.ajax({
        type: "POST",
        url: "/prueba/controllers/StoreController.php",
        data: {
            action: 'delete',
            store: {
                id: id_store,
            },
        }
    }).done(() => {
        get_stores();
        alert_success('eliminada');
    }).fail((jqXHR, textStatus, errorThrown) => {
        alert_error("Error al eliminar", textStatus)
    });
}

//#endregion

//#region FUNCIONES CRUD PRODUCTO


/**
 * Obtiene los productos de la base de datos y llenamos la tabla de productos
 * al igual que retornamos las Bodegas para ser utilizadas en un select.
 * @returns Las Bodegas.
 */
const get_products = async () => {
    const $product_table = document.querySelector("#product_table");
    var stores = '';
    $product_table.innerHTML = "";
    await $.ajax({
        type: "GET",
        url: "/prueba/controllers/ProductController.php?action=get",
        dataType: "json",
    }).done((response) => {
        $.each((response[0]), (key, product) => {
            charge_table_product($product_table, product, key, response[1])
        });
        return stores = response[1];
    }).fail((jqXHR, textStatus, errorThrown) => {
        alert_error("Error al obtener los productos", errorThrown)
    });
    return stores;
}

/**
 * Consigue los productos en una Bodega y los muestra en una tabla.
 * @param id_store - id de la tienda
 * @param cod_store - es el codigo de la bodega
 * @param available_store - boolean que determina si esta disponible o no la bodega
 */
const get_product_in_store = (id_store, cod_store, available_store) => {
    const $store_product_table = document.querySelector("#product_store_table");
    $store_product_table.innerHTML = "";
    $.ajax({
        type: "POST",
        url: "/prueba/controllers/ProductController.php",
        dataType: 'json',
        data: {
            action: 'show',
            id_store: id_store
        }
    }).done((response) => {
        if (response === false) {
            const $fill = document.createElement("td");
            $fill.colSpan = 5;
            const $text = document.createElement("h6");
            $text.className = "text-center"
            $text.innerText = 'No hay productos en esta bodega';
            $fill.appendChild($text);
            $store_product_table.appendChild($fill)
        } else {
            $.each((response), (key, product) => {
                charge_table_product_store($store_product_table, product, key)
            });
        }
        const $button_available = document.getElementById('button_available');

        $button_available.value = available_store;
        available_store == 't' ?
            $button_available.innerText = 'Inhabilitar Bodega' :
            $button_available.innerText = 'Habilitar Bodega';
        $button_available.value = id_store;

        const $title_store = document.getElementById('title_store');
        $title_store.innerText = `Bodega ${cod_store}`;
        $('#modal_store').modal('show');
    }).fail((jqXHR, textStatus, errorThrown) => {
        alert_error("Error al obtener productos en bodega", errorThrown)
    });
}

/**
 * Es una función que agrega un producto a la base de datos.
 * @param stores - array de Bodegas
 */
const add_product = (stores) => {
    const $button_add = document.getElementById("button_add_product");
    $button_add.addEventListener('click', () => {
        document.getElementById("title_modal").innerHTML = "Añadir Producto";
        document.getElementById("lb_store").innerHTML = "Bodegas";
        $input_action = document.getElementById("action_product");
        $input_action.value = "add";
        modal_input_clean(stores);
        $('#modal_product').modal('show');
        var form_add = document.getElementById('form_product');

        form_add.addEventListener("submit", (event) => {
            event.preventDefault();
            if ($input_action.value === 'add') {
                var name_product = document.getElementById("name_product").value;
                var price_product = document.getElementById("price_product").value;
                var stock_product = document.getElementById("stock_product").value;
                var id_store = document.getElementById("store_product").value;
                $.ajax({
                    type: "POST",
                    url: "/prueba/controllers/ProductController.php",
                    data: {
                        action: 'add',
                        product: {
                            name: name_product,
                            price: price_product,
                            stock: stock_product,
                            id_store: id_store
                        },
                    }
                }).done((response) => {
                    get_products();
                    $('#modal_product').modal('hide');
                    alert_success('Entrada guardada');
                    return 0;
                }).fail((jqXHR, textStatus, errorThrown) => {
                    alert_error("Error al guardar", textStatus)
                });
            }
        });

    });


}

/**
 * Es una función que se llama cuando el usuario hace click en el botón "guardar" del modal, toma los
 * valores de las entradas del modal y los envía al controlador para actualizar el producto en la
 * base de datos.
 */
const edit_product = () => {

    var form_edit = document.getElementById("form_product");
    form_edit.addEventListener("submit", (event) => {
        event.preventDefault();
        var id_store = document.getElementById('store_product').value;
        var name_product = document.getElementById("name_product").value;
        var price_product = document.getElementById("price_product").value;
        var stock_product = document.getElementById("stock_product").value;
        if (document.getElementById("action_product").value === 'edit') {
            $.ajax({
                type: "POST",
                url: "/prueba/controllers/ProductController.php",
                data: {
                    action: 'edit',
                    product: {
                        id: document.getElementById("button_save").value,
                        name: name_product,
                        price: price_product,
                        stock: stock_product,
                        id_store: id_store
                    },
                }
            }).done(() => {
                get_products();
                $('#modal_product').modal('hide');
                alert_success('actualizada');
                return 0;
            }).fail((jqXHR, textStatus, errorThrown) => {
                alert_error("Error al editar", textStatus)
            });
        }
    });

}


/**
 * Envía una solicitud AJAX y, si tiene éxito, llama a la función get_products()function
 * y la función alert_success().
 * @param id_product - El id del producto que se va a eliminar.
 */
const delete_product = (id_product) => {
    $.ajax({
        type: "POST",
        url: "/prueba/controllers/ProductController.php",
        data: {
            action: 'delete',
            product: {
                id: id_product,
            },
        }
    }).done(() => {
        get_products();
        alert_success('eliminada');
    }).fail((jqXHR, textStatus, errorThrown) => {
        alert_error("Error al eliminar", textStatus)
    });
}

//#endregion

//#endregion

//#region TABLAS


/**
 * Se crea una fila de la tabla, luego crea una celda de la tabla para cada propiedad del objeto del producto, luego
 * crea dos botones, uno para editar y otro para eliminar, y luego agrega la fila a la tabla.
 * @param $product_table - la tabla donde se mostrarán los datos
 * @param product - Es un objeto que contiene los datos del producto
 * @param key - El índice del elemento actual que se está procesando en el array.
 * @param stores - Es un array de objetos que contiene las Bodegas.
 */
const charge_table_product = ($product_table, product, key, stores) => {

    const $fill = document.createElement("tr");
    //Inyectamos los datos
    const $cell_count = document.createElement("td");
    $cell_count.innerText = key + 1;
    $fill.appendChild($cell_count);
    // Celda codigo del producto
    const $cell_cod_product = document.createElement("td");
    $cell_cod_product.innerText = product.cod_product;
    $fill.appendChild($cell_cod_product);
    // Celda nombre
    const $cell_name = document.createElement("td");
    $cell_name.innerText = product.name;
    $fill.appendChild($cell_name);
    // Celda precio
    const $cell_price = document.createElement("td");
    $cell_price.innerText = product.price;
    $fill.appendChild($cell_price);
    // Celda stock
    const $cell_stock = document.createElement("td");
    $cell_stock.innerText = product.stock;
    $fill.appendChild($cell_stock);
    // Celda fecha creación
    const $cell_created_at = document.createElement("td");
    $cell_created_at.innerText = new Date(product.created_at).toLocaleDateString('es-ES', { day: "numeric", month: "2-digit", year: "numeric" })
    $fill.appendChild($cell_created_at);

    // Celda stock
    const $cell_store = document.createElement("td");
    $cell_store.innerText = product.cod_store;
    $fill.appendChild($cell_store);

    // boton Editar
    const $cell_button = document.createElement("td");
    const $button_edit = document.createElement("button");
    $button_edit.innerHTML = `<i class="fa fa-edit"></i>`;
    $button_edit.id = 'button_edit';
    $button_edit.classList.add("btn", "btn-warning");
    $button_edit.onclick = async () => {
        document.getElementById("lb_store").innerHTML = "Cambiar Bodega";
        $button_save = document.getElementById("button_save");
        $input_action = document.getElementById("action_product");
        $input_action.value = "edit";
        $button_save.value = product.id_product;
        $('#modal_product').modal('show');

        //Llamamos al metodo modal padding para pasarle el producto
        modal_padding(product, stores);

    }
    $cell_button.appendChild($button_edit);


    // boton eliminar
    const $button_delete = document.createElement("button");
    $button_delete.innerHTML = `<i class="fa fa-trash"></i>`;
    $button_delete.classList.add("btn", "btn-danger");
    $button_delete.value = product.id_product;
    $button_delete.onclick = async () => {
        //Alerta eliminar producto
        await alert_delete(product.name).then((value) => {
            if (value == true) {
                delete_product($button_delete.value)
            }
        });
    };

    $cell_button.appendChild($button_delete);
    $fill.appendChild($cell_button);

    // Inyectamos las filas
    $product_table.appendChild($fill);
}

/**
 * Crea una fila de tabla, la llena con datos y la agrega a la tabla
 * @param $store_table -La tabla donde se mostrarán los datos.
 * @param store - Una bodega en particular
 * @param key - El índice del elemento actual que se está procesando en la matriz..
 */
const charge_table_store = ($store_table, store, key) => {
    const $fill = document.createElement("tr");
    //Inyectamos los datos
    const $cell_count = document.createElement("td");
    $cell_count.innerText = key + 1;
    $fill.appendChild($cell_count);
    // Celda codigo de la bodega
    const $cell_cod_store = document.createElement("td");
    $cell_cod_store.innerText = store.cod_store;
    $fill.appendChild($cell_cod_store);
    // Celda fecha creación
    const $cell_created_at = document.createElement("td");
    $cell_created_at.innerText = new Date(store.created_at).toLocaleDateString('es-ES', { day: "numeric", month: "2-digit", year: "numeric" })
    $fill.appendChild($cell_created_at);

    // Celda codigo de la bodega
    const $cell_available = document.createElement("td");
    $cell_available.innerText = store.available === 't' ? 'Disponible' : 'No disponible';
    $fill.appendChild($cell_available);
    //Botones
    const $cell_button = document.createElement("td");
    //boton ver bodega
    const $button_show = document.createElement("button");
    $button_show.innerHTML = `<i class="fa-solid fa-eye"></i>`;
    $button_show.classList.add("btn", "btn-primary");
    $button_show.value = store.id_store;
    $button_show.id = 'button_show_store'
    $button_show.onclick = () => {
        get_product_in_store(store.id_store, store.cod_store, store.available);

    };
    $cell_button.appendChild($button_show);

    // boton eliminar
    const $button_delete = document.createElement("button");
    $button_delete.innerHTML = `<i class="fa fa-trash"></i>`;
    $button_delete.classList.add("btn", "btn-danger");
    $button_delete.value = store.id_store
    $button_delete.onclick = async () => {
        //Alerta eliminar producto
        await alert_delete(store.cod_store).then((value) => {
            if (value == true) {
                delete_store($button_delete.value)
            }
        });
    };

    $cell_button.appendChild($button_delete);
    $fill.appendChild($cell_button);

    // Inyectamos las filas
    $store_table.appendChild($fill);
}


/**
 * Crea una fila de tabla, crea una celda de tabla para cada propiedad del objeto de producto y
 * agrega la fila de la tabla a la tabla.
 * @param $product_table - La tabla donde se mostrarán los datos.
 * @param product - Es un objeto que contiene los datos del producto.
 * @param key - El índice del elemento actual que se está procesando en la matriz.
 */
const charge_table_product_store = ($product_store_table, product, key) => {
    const $fill = document.createElement("tr");
    //Inyectamos los datos
    const $cell_count = document.createElement("td");
    $cell_count.innerText = key + 1;
    $fill.appendChild($cell_count);
    // Celda codigo del producto
    const $cell_cod_product = document.createElement("td");
    $cell_cod_product.innerText = product.cod_product;
    $fill.appendChild($cell_cod_product);
    // Celda nombre
    const $cell_name = document.createElement("td");
    $cell_name.innerText = product.name;
    $fill.appendChild($cell_name);
    // Celda precio
    const $cell_price = document.createElement("td");
    $cell_price.innerText = product.price;
    $fill.appendChild($cell_price);
    // Celda stock
    const $cell_stock = document.createElement("td");
    $cell_stock.innerText = product.stock;
    $fill.appendChild($cell_stock);
    // Inyectamos las filas
    $product_store_table.appendChild($fill);
}
//#endregion

//#region MODAL
// Funciones para Modal


/**
 * Toma dos argumentos, un producto y una lista de Bodega, y luego completa un elemento de selección con
 * las Bodegas.
 * @param product - Es un objeto con los datos del producto
 * @param stores - Array de Bodega
 */
const modal_padding = (product, stores) => {
    // Apuntamos a los input para pasar los atributos de producto
    document.getElementById("title_modal").innerHTML = "Editar Producto";
    document.getElementById("name_product").value = product.name;
    document.getElementById("price_product").value = product.price;
    document.getElementById("stock_product").value = product.stock;
    const select_stores = document.getElementById("store_product")
    // Ciclo for para limpiar las option para no generar duplicados
    for (const option of [...select_stores.options]) {
        option.remove();
    }
    stores.forEach((store) => {
        if (store.available == 't') {
            const option = document.createElement("option");
            option.value = store.id_store;
            option.text = store.cod_store;
            if (product.store_id === store.id_store) {
                option.selected = "selected"
                option.style = "color:black; font-weight: bold;"
            }
            select_stores.appendChild(option);
        }
    });
}

/**
 * Toma una lista de las Bodegas y las agrega a un elemento seleccionado
 * @param stores - Es una lista de objetos que contiene las Bodegas que están disponibles para ser seleccionadas en el modal.
 */
const modal_input_clean = (stores) => {
    document.getElementById("name_product").value = "";
    document.getElementById("price_product").value = "";
    document.getElementById("stock_product").value = "";
    const select_stores = document.getElementById("store_product")
    for (const option of [...select_stores.options]) {
        option.remove();
    }
    stores.forEach((store) => {
        if (store.available == 't') {
            const option = document.createElement("option");
            option.style = "color: black";
            option.value = store.id_store;
            option.text = store.cod_store;
            select_stores.appendChild(option);
        }
    });
}

//#endregion

//#region ALERTAS

//Para las alertas personalizadas se utilizo sweet alert 2.
/**
 * Toma dos argumentos, un mensaje y un error, y los muestra en una ventana emergente.
 * @param message - El mensaje que se mostrará en la alerta.
 * @param error - El mensaje de error que desea mostrar.
 */
const alert_error = (message, error) => {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
        footer: error
    })
}

/**
 * Toma una cadena como argumento y muestra una alerta de éxito con la cadena como mensaje.
 * @param message - El mensaje que se mostrará en la alerta.
 */
const alert_success = (message) => {
    Swal.fire({
        icon: 'success',
        title: `Entrada ${message}`,
        showConfirmButton: false,
        timer: 1500
    });
}

/**
 * Devuelve una promesa que se resuelve en un valor booleano.
 * @param message - El mensaje que se mostrará en la alerta.
 * @returns Una promesa.
 */
const alert_delete = async (message) => {
    const response_confirm = await Swal.fire({
        title: "Confirmación",
        text: `¿Eliminar ${message}?`,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    return response_confirm.value
}

/**
 * Devuelve una promesa que se resuelve en un valor booleano.
 * @returns Una promesa.
 */
const alert_confirm = async () => {
    const response_confirm = await Swal.fire({
        title: "Hey",
        text: `¿Deseas agregar una nueva bodega?`,
        icon: 'question',
        showCancelButton: true,
        cancelButtonColor: '#3085d6',
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    });

    return response_confirm.value
}

//#endregion