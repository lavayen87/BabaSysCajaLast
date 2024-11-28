   
    var moneda_ingreso = 'pesos';
    var moneda_egreso = '';
    var caja = '';
    var numero_caja = 0;

    var lista_num_caja ={
        'Cajero1':1,
        'Admin1':2, 
        'Admin2':3, 
        'AdminBC':4,
        'Luis B.':5,
        'Sergio B.':6,
        'Daniel B.':7,
        'Ariel M.':8,
        'Legales':9,
        'Tesoro':10,
        'Banco': 11,
        'Morena Beltran':17,
        'Admin-Luis':34
    };

    var lista_num_caja2 ={
        'Admin-Luis':0,
        'Cajero1':1,
        'Admin2':2, 
        'Banco':3, 
        'Tesoro':4,
        'Luis B.':5,
        'Sergio B.':6,
        'Daniel B.':7,
        'Ariel M.':8,
        'Admin1':9,
        'Legales':10,
        'AdminBC':11,
        'Administrador': 12,
        'Morena Beltran':17
    };

    var lista_cajas ={
            1:'caja_cajero',
            2:'caja_matias_b', 
            3:'caja_banco', 
            4:'caja_euros', 
            5:'caja_luis_b',
            6:'caja_sergio_b',
            7:'caja_daniel_b',
            8:'caja_ariel_m',
            9:'caja_liliana',
            10:'caja_legales',
            12:'caja_tesoro',
            17:'Morena Beltran',
            20:'caja_20',
            21:'caja_21',
            22:'caja_22',
            34:'caja_luis_l'
    };
    
    var lista_cajas2 ={
        0:'Admin-Luis',
        1:'Cajero1',
        2:'Admin2',
        3:'Banco', 
        4:'Tesoro', 
        5:'Luis B.',
        6:'Sergio B.',
        7:'Daniel B.',
        8:'Ariel M.',
        9:'Admin1',
        10:'Legales',
        11:'AdminBC',
        12:'administrador',
        17:'Morena Beltran'
    };
            

    var moneda_tr = '';
    let plantilla = '';
    let datos_tr = '';
    var id=0;
    var e=0;
    var f=0;
    
    

$(window).ready(function () {


    // if (!window.navigator.onLine) {
    //     alert('No hay conexion a internet');
    // }


    setTimeout(function () {
        // version mejorada
        function ajaxPositive(response) {
            console.log('response.ok: ', response.ok);
            if (response.ok) {
                response.text().then(showResult);
            } else {
                showError('status code: ' + response.status);
            }
        }

        function showResult(txt) {
            console.log('muestro respuesta:' + '\n', txt);
        }

        function showError(err) {
            console.log('muestro error', err);
            //alert('No hay conexión a internet');  
        }

        fetch('loteos.txt')
            .then(ajaxPositive)
            .catch(showError);

        // version 1    
        /*fetch('loteos.txt')
        .then(res => res.text())
        .then(content => {
          let lines = content.split(/\n/);
          lines.forEach(line => console.log(line));
            
        })
        .catch(function(error) {
            console.log(error);
        });*/


        // version 2
        /*fetch('loteosd.txt')
        .then( response => response.text() )
        .then( resultText => console.log(resultText) )
        .catch( err => console.log('Error = '+err) );*/


    }, 1000);

    var usuarios = [];
    $.get('get_usuarios.php', (resp) => {
        let datos = JSON.parse(resp);
        datos.forEach(d => {

            usuarios.push(d.rol);
        })
    })

    // ocultar cobranza y monto
    $.get('ocultar_option.php', (resp) => {
        if (resp != 'usuario inactivo') {
            
            var c = parseInt(resp);
            console.log("caja actual: " + c);
            let datos;
            var lista_cajas = [];
            $.get('CajasCobranza.php', (cajas) => {
                if (cajas != parseInt(0)) {
                     
                    datos = JSON.parse(cajas);
                    datos.forEach(d => {
                        lista_cajas.push(parseInt(d));
                    });
                   
                    //console.log(lista_cajas)
                }

                if (lista_cajas.indexOf(c) == parseInt(-1)) {
                    console.log("index of 1: " + lista_cajas.indexOf(c))
                    // oculto cobranza
                    $("strong[id='id-cobranza']").html("");
                    $("strong[id='id-monto']").html("");
                    // coulto servicios
                    $("strong[id='id-monto-serv']").html("");
                    $("strong[id='monto-serv']").html("");
                }
                else {
                    console.log("index of 2: " + lista_cajas.indexOf(c))
                }

            })
            
            
        }
        else {
            console.log('usuario inactivo')
        }
    })

 
    ocultar();

    function ocultar()
    {
        $.get('ocultar_option.php', (resp)=>{       
            if(resp !='usuario inactivo') 
            {  
                $("#select-caja-destino option").each(function(){

                    //f ($(this).val() == lista_cajas2[parseInt(resp)]) //version original
                    if (usuarios.includes($(this).val()))
                    { 
                        $(this).hide();
                    }           
                });

                /*$("#select-num-caja option").each(function(){
                    
                    if($(this).prop('id') == lista_cajas2[parseInt(resp)])
                    { 
                        $(this).hide();
                    }           
                });*/
            }
        }) 
    }
    
    // mostrar ocultar boton imprimir listado de caja
    $('#btn-listar').on('click', function(){
        var fecha_inicial = $('#fecha_inicial').val();
      
        var fecha_final = $('#fecha_final').val();
                $.post('listarcaja.php', {'fecha_inicial': fecha_inicial, 'fecha_final': fecha_final}, resp=>{
            $('#content-listado').html(resp);
        })
    })
    $('#print-listado').on('click', function(){
        var fecha_inicial = $('#fecha_inicial').val();
        var fecha_final = $('#fecha_final').val();
        $.post('factura/prueba-print.php', {'fecha_inicial': fecha_inicial, 'fecha_final': fecha_final}, resp=>{
            window.open('factura/prueba-print.php', '_blank');
        })
    })
    
    
    //Login - logout
    $('.name-user').on('click', function(){
        if($('#sub-hide').is(':visible'))
            $('#sub-hide').slideUp('fast');
        else
            $('#sub-hide').slideDown('fast');
    })

    $('#close-sesion').on('click',function(){
        $.get('close-sesion.php', (resp)=>{
            console.log(resp)
            if(resp == 'ok')
                window.location = 'index.php'; 
        })
        
    });


    $('.popup-overlay').on('click', function(){
        $('#alert').hide()
        $('#popup-orden-pago').fadeOut('slow');
        $('#popup-fondos').fadeOut('fast');
        $('.popup-overlay').fadeOut('slow');
        return false;
    })

    $('#compras').on('click', function(){
        $('#popup-compras').fadeIn('fast');
    })

    $('#close-compras').on('click', function(){
        $('#popup-compras').fadeOut('fast');
    })
    

    /* CODIGO 'ORDEN DE PAGO' AQUI */


    /*------------------------------------------------------- */
    //ORDEN DE PAGO CON CHEQUE
    var cuenta_cheq = "";
    var detalle_od_cheq = "";
    var receptor = "";
    var importe_od_cheq = parseInt(0);
    var empresa_cheq = "";
    var obra_cheq = "";
    var id_empresa = parseInt(0);
    var n_cheques = parseInt(0);

    $("#select-empresa-cheq").on('change', function(){
        empresa_cheq = $("#select-empresa-cheq").val();
        /*id_empresa = $("#select-empresa-cheq optionselected").attr('id');*/
        console.log('empresa: '+empresa_cheq)
    })

    $("#select-obra-cheq").on('change', function(){
        obra_cheq = $("#select-obra-cheq").val();
        console.log('obra: '+obra_cheq)
    })

    $('#select-cuenta-cheq').on('change', function(){
        cuenta_cheq = $('#select-cuenta-cheq').val();
        console.log('cuenta: '+cuenta_cheq)
    })

    $("#importe-op-cheq").keyup(function(){
        importe_od_cheq = $('#importe-op-cheq').val();
        console.log('importe: '+importe_od_cheq)
    });

    $("#receptor-op-cheq").keyup(function(){
        receptor = $('#receptor-op-cheq').val();
        console.log('Recibe: '+receptor)
       });

    $("#detalle-op-cheq").keyup(function(){
     detalle_od_cheq = $('#detalle-op-cheq').val();
     console.log('detalle: '+detalle_od_cheq)
    });        

    // Carga de importes / orden de pago con cheques
    var id_check = parseInt(0);
    var lista_ids_chq = [];
    $('input[name=check]').on('click', function(event){
        if($(this).val() == 0)
        {
            $(this).val('1'); // seleccionado
            id_check = $(this).prop('id'); 
            var valor = $('.tabla-chq tr[id='+parseInt(id_check)+']').find(".importe-chq").children().val();
            
            if(n_cheques < parseInt(8))
            {
                lista_ids_chq.push(id_check);
                console.log(lista_ids_chq);
                console.log(valor);
                importe_od_cheq+=Number(valor);
                n_cheques++;
                $('#importe-op-cheq').val(importe_od_cheq);
            }
            else{
                $(this).val('0'); // deseleccionado
                $(this).prop("checked", false);
                //alert('No puede cargar mas de 8 cheques');
                var info = "<strong>No puede cargar mas de 8 cheques.</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
            }           
        }
        else{
            $(this).val('0'); // Deseleccionado
            id_check = $(this).prop('id'); 
            var valor = $('.tabla-chq tr[id='+parseInt(id_check)+']').find(".importe-chq").children().val();
            
            if(find_index(lista_ids_chq, id_check) >= parseInt(0))// verifico si existe el id en la lista                 
            {
                var j = find_index(lista_ids_chq, id_check);
                lista_ids_chq.splice(j, 1); // quito el id de la lista
                console.log(lista_ids_chq);
                n_cheques--;
            }

            console.log(valor);
            importe_od_cheq-=Number(valor);
            
            $('#importe-op-cheq').val(importe_od_cheq);
             
        }
        
    })

    $('#aceptar-op-cheq').on('click', function(){                    
        if( (importe_od_cheq > parseInt(0))  && (importe_od_cheq !="") && (detalle_od_cheq !="") && (cuenta_cheq !="") 
            && (empresa_cheq !="") && (obra_cheq !="") && (receptor !=""))
        {
            console.log('importe: '+importe_od_cheq)
            console.log('detalle: '+detalle_od_cheq)
            console.log('cuenta: '+cuenta_cheq)

            if($('#exito-op-cheq').is(':visible')){
                $('#exito-op-cheq').hide();
                $('#content-cheq').show();
            }
            $.post('orden_pago_cheque.php', {'empresa': empresa_cheq,'obra': obra_cheq,'cuenta':cuenta_cheq, 'importe': importe_od_cheq, 'detalle': detalle_od_cheq,'lista_ids':lista_ids_chq,'receptor_cheque':receptor}, (resp) =>{
                console.log(resp)
                if(resp = 'ok')
                { 
                    //EFECTO LOADING   
                    //Añadimos la imagen de carga en el contenedor
                    $('#content-cheq').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "POST",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-cheq').fadeIn(1000).html("");
                            $('#exito-op-cheq').slideDown(); 
                            $("#select-empresa-cheq").val("");
                            $("#select-obra-cheq").val("");
                            $('#select-cuenta-cheq').val("");
                            $('#receptor-op-cheq').val("");
                            $('#importe-op-cheq').val("");
                            $("#detalle-op-cheq").val("");
                            empresa_cheq    = "";
                            obra_cheq       = "";
                            cuenta_cheq     = "";
                            importe_od_cheq = "";
                            detalle_od_cheq = ""; 
                            receptor = "";
                            // deseleccionar checkbox  
                            $('input[name=check]').each(function(){
                                if($(this).val() == '1'){
                                    $(this).val('0');
                                    $(this).prop("checked", false);
                                }
                            });              
                            
                            // borrar filas seleccionadas para el chque
                            $.each(lista_ids_chq, function (ind, elem) { 
                                $('tr[id='+parseInt(elem)+']').remove();
                            });                            

                            lista_ids_chq = [];
                            console.log(lista_ids_chq)

                            if($('.tabla-chq').find('tbody tr').length == parseInt(0))
                            {
                                $('.content-table-scroll').slideUp();
                                $('#importe-op-cheq').attr('readonly',true);
                            }
                        }
                    });
                    return false;
                                       
                }
                else
                { 
                    $("#select-empresa-cheq").val("");
                    $("#select-obra-cheq").val("");
                    $('#select-cuenta-cheq').val("");
                    $('#receptor-op-cheq').val("");
                    $('#importe-op-cheq').val("");
                    $("#detalle-op-cheq").val("");
                    empresa_cheq    = "";
                    obra_cheq       = "";
                    cuenta_cheq     = "";
                    importe_od_cheq = "";
                    detalle_od_cheq = ""; 
                    receptor = "";   
                    var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })       
                }
            });
        }
        else{
            //alert('Debe llenar todos los campos.');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
        }
    })     
 
    //Cancelar orden de pago con cheque
    $('#cancelar-op-cheq').on('click', function(){
        window.location = 'file_orden_pago_cheque.php';       
    }) 

    $('#btn-print-op-cheq').on('click', function(){
       // $('#content-cheq').html("");
        $('#exito-op-cheq').slideUp();     
    }) 

    // cerrar ventana exito orden de pago con cheque
    $('#cerrar-content-op-cheq').on('click',function(){
        //$('#content-cheq').html("");
        $('#exito-op-cheq').slideUp();
        $("#select-empresa-cheq option[value='']").attr("selected",true);
        $("#select-obra-cheq option[value='']").attr("selected",true);
        $("#select-cuenta-cheq option[value='']").attr("selected",true);
        $("#importe-op-cheq").val("");
        $("#detalle-op-cheq").val("");
        empresa_cheq = "";
        obra_cheq = "";
        cuenta_cheq = "";
        importe_od_cheq = "";
        detalle_od_cheq = "";    
    })

    $('#nueva-op-cheq').on('click', function(){
        window.location = 'file_orden_pago_cheque.php';  
    })

    ///////////////////////////////////

    //cerrar ventana alerta orden de pago
    $('#cerrar-alerta-op').on('click', function(){
        $('#alerta-op').fadeOut('fast');
    })
    //end orden pago

    /* ------------------------------------------------- */

    //Solicitud de Orden de pago
    var moneda_so = "";
    var caja_pago = "";
    var solicitante = "";
    var cuenta_sod = "";
    var receptor_solicitud_op = "";
    var detalle_sod = "";
    var importe_sod = parseInt(0);
    var empresa_sod = "";
    var obra_sod = "";
    var id_empresa = parseInt(0);
    var recibe = "";
    var importe_mi_check = parseInt(0);
    var importe_check_cart = parseInt(0);
    var id_mi_check = parseInt(0);
    var id_check_cart = parseInt(0);
    var lista_ids_mi_chq = [];
    var lista_ids_chq_cart = [];
    var lista_cajas = [];
    var opcion_pago = parseInt(1);
    var cant_selec = parseInt(0);
    var caja_check_list = parseInt(0); 
    var caja_select = parseInt(0);

    function find_index(array, value){
        var i = parseInt(0);
        var pos = parseInt(0);
        while(i < array.length){
          if(array[i] == value)
          { 
            pos = parseInt(i);
            i = parseInt(array.length)+ parseInt(10);
            
          }
          else i = i + parseInt(1);
        }
        if(i > parseInt(array.length))
        {
            return pos;
        }
        else return(parseInt(-1));
    };

    //opcion de pago
    $('.item1').on('click',function(){
        opcion_pago = parseInt(1);  
        console.log('opcion pago: '+opcion_pago)
    })

    //opcion de pago con mis cheques
    $('.item2').on('click',function(){
        opcion_pago = parseInt(2);  
        console.log('opcion pago: '+opcion_pago)
    })

    //opcion de pago con cheques en cartera
    $('.item3').on('click',function(){
        opcion_pago = parseInt(3);  
        console.log('opcion pago: '+opcion_pago)
    })


    // Mis cheques
    if($('.tabla-mis-cheques tr').length > parseInt(0))
    {
        
        $('#importe-my-check').prop('disabled', 'disabled');
        $('#importe-my-check').css('background-color', 'white');
        $('#importe-my-check').val("");
            //$('.mis_cheques').slideDown();
            
        // checkbox seleccionados
        $('input[name=mi_check]').on('click', function(){
            if($(this).val() == '0')
            {
                
                $(this).val('1'); // seleccionado
                $(this).prop("checked", true);
                
                id_mi_check = $(this).prop('id');              
                var valor = $('.tabla-mis-cheques tr[id='+parseInt(id_mi_check)+']').find(".importe-chq").children().val();                
                
                if(cant_selec < parseInt(8))
                {
                    lista_ids_mi_chq.push(id_mi_check); // cargo id
                    console.log(lista_ids_mi_chq);
                    importe_check_cart+=Number(valor); 
                    cant_selec++;   
                    console.log('cant selec: '+cant_selec); 
                    importe_mi_check+=Number(valor);
                    //importe_sop_cheq+=Number(valor);
                    
                    $('#importe-my-check').val(importe_mi_check);    
                } 
                else 
                {
                    $(this).val('0'); // deseleccionado
                    $(this).prop("checked", false);
                    //alert('No puede cargar mas de 8 cheques.'); 
                    var info = "<strong>No puede cargar mas de 8 cheques.</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    }) 
                }
                
                /*lista_ids_mi_chq.push(id_mi_check);
                console.log(lista_ids_mi_chq);
                console.log(valor);
                
                cant_selec++;
                console.log('cant selec: '+cant_selec);
                importe_mi_check+=Number(valor);
                //importe_sop_cheq+=Number(valor);
                
                $('#importe-my-check').val(importe_mi_check);*/
                        
            }
            else{
                
                $(this).val('0'); // Deseleccionado 
                $(this).prop("checked", false);
                
                id_mi_check = $(this).prop('id'); 
                var valor = $('.tabla-mis-cheques tr[id='+parseInt(id_mi_check)+']').find(".importe-chq").children().val();
                if(find_index(lista_ids_mi_chq, id_mi_check) >= parseInt(0))// verifico si existe el id en la lista                 
                {
                    var j = find_index(lista_ids_mi_chq, id_mi_check);
                    lista_ids_mi_chq.splice(j, 1); // quito el id de la lista
                    console.log(lista_ids_mi_chq);
                    cant_selec--;
                    console.log('cant selec: '+cant_selec);
                }
    
                importe_mi_check-=Number(valor);
                //importe_od_cheq-=Number(valor);
                
                $('#importe-my-check').val(importe_mi_check);
                
            }
                        
        })
        
    }

       
    // Cheques en cartera
    if($('.tabla-cheques-cartera tr').length > parseInt(0))
    {

        $('#importe-check-list').val("");

        if(importe_mi_check != parseInt(0))
        {
            importe_mi_check = parseInt(0);               
            
        }
        
        // checkbox seleccionados
        $('input[name=check_cart]').on('click', function(e){
            e.preventDefault;
            if($(this).val() == '0')
            {
                $(this).val('1'); // seleccionado
                $(this).prop("checked", true);

                id_check_cart = $(this).prop('id');

                var valor = $('.tabla-cheques-cartera tr[id='+parseInt(id_check_cart)+']').find(".importe-chq").children().val(); 
                var caja = $('.tabla-cheques-cartera tr[id='+parseInt(id_check_cart)+']').find(".caja-chq").children().val(); 
                caja_pago = caja;
                console.log('caja pago: '+caja_pago);

                if(lista_cajas.length == parseInt(0)) // primera carga
                {
                    lista_ids_chq_cart.push(id_check_cart); // cargo id
                    lista_cajas.push(caja); // cargo caja
                    importe_check_cart+=Number(valor);
                    cant_selec++;
                    console.log('cant selec: '+cant_selec); 
                    $('#select-caja-check-list').val(caja);  
                }
                else
                {
                    if(find_index(lista_cajas, caja) >= parseInt(0))
                    {
                        if(cant_selec < parseInt(8))
                        {
                            lista_ids_chq_cart.push(id_check_cart); // cargo id
                            importe_check_cart+=Number(valor); 
                            cant_selec++;   
                            console.log('cant selec: '+cant_selec);     
                        } 
                        else 
                        {
                            $(this).val('0'); // deseleccionado
                            $(this).prop("checked", false);
                            //alert('No puede cargar mas de 8 cheques.');
                            var info = "<strong>No puede cargar mas de 8 cheques.</strong>";
                            $('#modal-info').html(info);
                            $('#miModal').slideDown();

                            $('.close-modal').on('click', function(){
                                $('#miModal').slideUp();
                            })  
                        }
                        
                    }
                    else{
                        $(this).val('0'); // deseleccionado
                        $(this).prop("checked", false);

                    }
                }

                //importe_check_cart+=Number(valor);
                console.log(lista_ids_chq_cart);
                console.log('importe chq cart: '+importe_check_cart)
                
                $('#importe-check-list').val(importe_check_cart);              
            }
            else
            {
                $(this).val('0');// Deseleccionado
                $(this).prop("checked", false);

                id_check_cart = $(this).prop('id'); 

                if(find_index(lista_ids_chq_cart, id_check_cart) >=parseInt(0))
                {
                    var j = find_index(lista_ids_chq_cart, id_check_cart);
                    lista_ids_chq_cart.splice(j, 1); // quito el id de la lista
                    var valor = $('.tabla-cheques-cartera tr[id='+parseInt(id_check_cart)+']').find(".importe-chq").children().val();               
                    importe_check_cart-=Number(valor);
                    cant_selec--;
                    console.log('cant selec: '+cant_selec); 
                    if(importe_check_cart == parseInt(0))
                    {
                        $('#select-caja-check-list').val("");
                        lista_cajas = [];
                        caja_pago = parseInt(0);
                    }
                    console.log(lista_ids_chq_cart);
                    console.log('importe chq cart: '+importe_check_cart)
                    $('#importe-check-list').val(importe_check_cart);
                }
                
                
            }
                        
        })
    
    }
    

    solicitante = $("#select-solisitante").val();
    console.log('solicitante: '+lista_num_caja2[solicitante])
    //console.log(solicitante);
    $("#select-moneda-so").on('change', function(){
        moneda_so = $("#select-moneda-so").val();
        if(moneda_so == 'cheques')
        {
            if($('#tabla_so_chq').is(':hidden')){
                $('#tabla_so_chq').slideDown();
            }
        }
        else{
            if($('#tabla_so_chq').is(':visible')){
                $('#tabla_so_chq').slideUp();
            }
        }
        console.log(moneda_so);
    })


    // solicitud de orden de pago en pesos, dolares o euros
    var moneda_se = parseInt(1);
    var tipoMoneda = 'pesos';

    $("#select-moneda-se").on('change', function(){
        moneda_se = $("#select-moneda-se").val();

        switch(moneda_se){
            case '1':
                tipoMoneda = 'pesos';
                console.log(tipoMoneda)
                break;
            case '2':
                tipoMoneda = 'dolares';
                console.log(tipoMoneda)
                break;
            case '3':
                tipoMoneda = 'euros';
                console.log(tipoMoneda)
                break;
        }
        
        console.log("moneda s.e.: "+moneda_se);
    })


    $("#select-caja").on('change', function(){
        caja_pago = $("#select-caja").val();
        console.log(caja_pago);
    })

    $("#select-empresa-solicitud").on('change', function(){
        empresa_sod = $("#select-empresa-solicitud").val();
        console.log(empresa_sod)
    })

    $("#select-obra-solicitud").on('change', function(){
        obra_sod = $("#select-obra-solicitud").val();
        console.log(obra_sod)
    })

    $('#select-cuenta-solicitud').on('change', function(){
        cuenta_sod = $('#select-cuenta-solicitud').val();
        console.log(cuenta_sod)
    })

    $('#receptor-solicitud-op').on('change', function(){
        receptor_solicitud_op = $('#receptor-solicitud-op').val();
        console.log(receptor_solicitud_op)
    })

    $("#importe-solicitud-op").keyup(function(){
        importe_sod = $('#importe-solicitud-op').val();
        console.log(importe_sod)
    });

    $("#recibe-cheque").keyup(function(){
        recibe = $('#recibe-cheque').val();
        console.log(recibe)
    });

    $("#detalle-solicitud-op").keyup(function(){
     detalle_sod = $('#detalle-solicitud-op').val();
     console.log(detalle_sod)
    }); 
    
    /** */
    var empresa_banco = "";
    var obra_banco = "";
    var cuenta_banco = "";
    var importe_banco = parseInt(0);
    var recibe_banco = "";
    var detalle_banco = "";
    $("#select-empresa-banco").on('change', function(){
        empresa_banco = $("#select-empresa-banco").val();
        console.log(empresa_banco)
    })

    $("#select-obra-banco").on('change', function(){
        obra_banco = $("#select-obra-banco").val();
        console.log(obra_banco)
    })

    $('#select-cuenta-banco').on('change', function(){
        cuenta_banco = $('#select-cuenta-banco').val();
        console.log(cuenta_banco)
    })

    $("#importe-solicitud-banco").keyup(function(){
        importe_banco = $('#importe-solicitud-banco').val();
        console.log(importe_banco)
    });

    $("#recibe-banco").keyup(function(){
        recibe_banco = $('#recibe-banco').val();
        console.log(recibe_banco)
    });

    $("#detalle-solicitud-banco").keyup(function(){
     detalle_banco = $('#detalle-solicitud-banco').val();
     console.log(detalle_banco)
    }); 
    /** */
    // Confirmar solicitud en efectivo
    $('#aceptar-solicitud-cash').on('click', function(){ 
        if( (caja_pago != "") && (importe_sod > parseInt(0))  && (importe_sod !="") &&(empresa_sod !="")  && (detalle_sod !="") && (obra_sod !="") && (cuenta_sod !="") && (receptor_solicitud_op !="") )
        {
            // var moneda = 'pesos';
            $.post('solicitud_orden_pago.php', {'empresa': empresa_sod,'obra': obra_sod,'cuenta':cuenta_sod, 'importe': importe_sod, 'detalle': detalle_sod, 'solicitante': solicitante, 'caja_pago':caja_pago, 'moneda':tipoMoneda, 'recibe':receptor_solicitud_op}, (resp) =>{
                console.log(resp)
                if(resp == 'ok')
                { 
                    $('#content-solicitud-op').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "POST",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-solicitud-op').fadeIn(1000).html(data+" <strong style='color: green;'>Solicitud creada con exito.</strong>");                           
                            $("#select-caja option[value='']").attr("selected",true);
                            $("#select-empresa-solicitud option[value='']").attr("selected",true);
                            $("#select-obra-solicitud option[value='']").attr("selected",true);
                            $("#select-cuenta-solicitud option[value='']").attr("selected",true);
                            $('#receptor-solicitud-op').val("");
                            $("#importe-solicitud-op").val("");
                            $("#detalle-solicitud-op").val("");
                            caja_pago = '';
                            solicitante = ''; 
                            empresa_sod = '';
                            obra_sod    = '';
                            cuenta_sod  = '';
                            importe_sod = parseInt(0);
                            detalle_sod = '';
                            $('#aceptar-solicitud-cash').fadeOut();
                        }
                    });
                    return false;
                                    
                }
                else
                { 
                    $("#select-caja option[value='']").attr("selected",true);
                    $("#select-empresa-solicitud option[value='']").attr("selected",true);
                    $("#select-obra-solicitud option[value='']").attr("selected",true);
                    $("#select-cuenta-solicitud option[value='']").attr("selected",true);
                    $('#receptor-solicitud-op').val("");
                    $("#importe-solicitud-op").val("");
                    $("#detalle-solicitud-op").val("");
                    caja_pago = '';
                    solicitante = '';
                    empresa_sod = '';
                    obra_sod    = '';
                    cuenta_sod  = '';
                    importe_sod = parseInt(0);
                    detalle_sod = ''; 
                    //alert('Error inesperado, intente nuevamente.');
                    var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })                
                }
            });
        }
        else {
            //alert('Debe llenar todos los campos');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
        }  
    })
    
    // Confirmar solicitud con mis cheques
    $('#aceptar-solicitud-my-check').on('click', function(){ 
        if( (importe_mi_check > parseInt(0))  && (empresa_sod !="")  && (detalle_sod !="") && (obra_sod !="") && (cuenta_sod !="") && (recibe !="") )
        {
            var moneda = 'cheques';
            $.post('solicitud_orden_pago.php', {'empresa': empresa_sod,'obra': obra_sod,'cuenta':cuenta_sod, 'importe': importe_mi_check, 'detalle': detalle_sod, 'solicitante': solicitante, 'lista_ids':lista_ids_mi_chq,'moneda':moneda, 'recibe':recibe}, (resp) =>{
                console.log(resp)
                if(resp == 'ok')
                { 
                    $('#content-solicitud-op').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "POST",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-solicitud-op').fadeIn(1000).html(data+" <strong style='color: green;'>Solicitud creada con exito.</strong>");                           
                            $("#select-caja option[value='']").attr("selected",true);
                            $("#select-empresa-solicitud option[value='']").attr("selected",true);
                            $("#select-obra-solicitud option[value='']").attr("selected",true);
                            $("#select-cuenta-solicitud option[value='']").attr("selected",true);
                            $("#recibe-cheque").val("");
                            $("#importe-my-check").val("");
                            $("#detalle-solicitud-op").val("");

                            // ocultar checkbox
                            $.each(lista_ids_mi_chq, function (ind, elem) { 
                                $('.tabla-mis-cheques tr[id='+parseInt(elem)+']').find("input[name='mi_check']").hide();
                            });
                            caja_pago = '';
                            solicitante = ''; 
                            empresa_sod = '';
                            obra_sod    = '';
                            cuenta_sod  = '';
                            importe_mi_check = parseInt(0);
                            detalle_sod = '';

                        }
                    });
                    return false;
                                    
                }
                else
                { 
                    $("#select-caja option[value='']").attr("selected",true);
                    $("#select-empresa-solicitud option[value='']").attr("selected",true);
                    $("#select-obra-solicitud option[value='']").attr("selected",true);
                    $("#select-cuenta-solicitud option[value='']").attr("selected",true);
                    $("#importe-solicitud-op").val("");
                    $("#detalle-solicitud-op").val("");
                    caja_pago = '';
                    solicitante = '';
                    empresa_sod = '';
                    obra_sod    = '';
                    cuenta_sod  = '';
                    importe_sod = parseInt(0);
                    detalle_sod = ''; 
                    //alert('Error inesperado, intente nuevamente.'); 
                    var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })               
                }
            });
        }
        else {
            //alert('Debe llenar todos los campos');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
        }
    })

    // Confirmar solicitud con cheques en cartera
    $('#aceptar-solicitud-check-list').on('click', function(){ 
        if( (importe_check_cart > parseInt(0))  && (empresa_sod !="")  && (detalle_sod !="") && (obra_sod !="") && (cuenta_sod !="") && (recibe !="") )
        {
            var moneda = 'cheques';
            $.post('solicitud_orden_pago.php', {'empresa': empresa_sod,'obra': obra_sod,'cuenta':cuenta_sod, 'importe': importe_check_cart, 'detalle': detalle_sod, 'solicitante': solicitante, 'lista_ids': lista_ids_chq_cart, 'caja_pago': caja_pago, 'moneda':moneda,'recibe':recibe}, (resp) =>{
                console.log(resp)
                if(resp == 'ok')
                { 
                    $('#content-solicitud-op').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "POST",
                        url: "sleep.php",
                        success: function(data)
                        {
                            //Cargamos finalmente el contenido deseado
                            $('#content-solicitud-op').fadeIn(1000).html(data+" <strong style='color: green;'>Solicitud creada con exito.</strong>");                           
                            $('#select-caja-check-list').val("");
                            $("#select-empresa-solicitud option[value='']").attr("selected",true);
                            $("#select-obra-solicitud option[value='']").attr("selected",true);
                            $("#select-cuenta-solicitud option[value='']").attr("selected",true);
                            $("#recibe-cheque").val("");
                            $("#importe-check-list").val("");
                            $("#detalle-solicitud-op").val("");

                            // ocultar checkbox
                            $.each(lista_ids_chq_cart, function (ind, elem) { 
                                $('.tabla-cheques-cartera tr[id='+parseInt(elem)+']').find("input[name='check_cart']").hide();
                            }); 

                            caja_pago = '';
                            solicitante = ''; 
                            empresa_sod = '';
                            obra_sod    = '';
                            cuenta_sod  = '';
                            importe_mi_check = parseInt(0);
                            detalle_sod = '';

                        }
                    });
                    return false;
                                    
                }
                else
                { 
                    $("#select-caja option[value='']").attr("selected",true);
                    $("#select-empresa-solicitud option[value='']").attr("selected",true);
                    $("#select-obra-solicitud option[value='']").attr("selected",true);
                    $("#select-cuenta-solicitud option[value='']").attr("selected",true);
                    $("#importe-check-list").val("");
                    $("#detalle-solicitud-op").val("");
                    caja_pago = '';
                    solicitante = '';
                    empresa_sod = '';
                    obra_sod    = '';
                    cuenta_sod  = '';
                    importe_sod = parseInt(0);
                    detalle_sod = '';  
                    //alert('Error inesperado, intente nuevamente.');  
                    var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })             
                }
            });
        }
        else {
            //alert('Debe llenar todos los campos');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            }) 
        }
    })
    
    // Confirmar solicitud con Banco
    $('#aceptar-solicitud-banco').on('click', function(){ 
        if( (importe_banco> parseInt(0))  && (empresa_banco !="")  && (detalle_banco !="") && (obra_banco !="") && (cuenta_banco !="") )
        {
            var moneda = 'pesos';
            caja_pago = parseInt(3);
            $.post('solicitud_orden_pago.php', {'empresa': empresa_banco,'obra': obra_banco,'cuenta':cuenta_banco, 'importe': importe_banco, 'recibe':recibe_banco, 'detalle': detalle_banco, 'solicitante': solicitante, 'caja_pago': caja_pago, 'moneda':moneda}, (resp) =>{
                console.log(resp)
                if(resp == 'ok')
                { 
                    $('#content-solicitud-banco').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "POST",
                        url: "sleep.php",
                        success: function(data)
                        {
                            //Cargamos finalmente el contenido deseado
                            $('#content-solicitud-banco').fadeIn(1000).html(data+" <strong style='color: green;'>Solicitud creada con exito.</strong>");                           
                            $("#select-empresa-banco option[value='']").attr("selected",true);
                            $("#select-obra-banco option[value='']").attr("selected",true);
                            $("#select-cuenta-banco option[value='']").attr("selected",true);                   
                            $("#recibe-banco").val("");
                            $("#importe-solicitud-banco").val("");
                            $("#detalle-solicitud-banco").val("");

                            caja_pago = '';
                            solicitante = ''; 
                            empresa_sod = '';
                            obra_sod    = '';
                            cuenta_sod  = '';
                            recibe_banco = "";
                            importe_sod = parseInt(0);
                            detalle_sod = '';

                        }
                    });
                    return false;
                                    
                }
                else
                { 
                    $("#select-empresa-banco option[value='']").attr("selected",true);
                    $("#select-obra-banco option[value='']").attr("selected",true);
                    $("#select-cuenta-banco option[value='']").attr("selected",true);                   
                    $("#recibe-banco").val("");
                    $("#importe-solicitud-banco").val("");
                    $("#detalle-solicitud-banco").val("");
                    caja_pago = '';
                    solicitante = '';
                    empresa_sod = '';
                    obra_sod    = '';
                    cuenta_sod  = '';
                    recibe_banco = "";
                    importe_sod = parseInt(0);
                    detalle_sod = '';  
                    //alert('Error inesperado, intente nuevamente.');  
                    var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })             
                }
            });
        }
        else {
            //alert('Debe llenar todos los campos');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
            
        }
    })
    

    //Cancelar solicitud de orden de pago
    $('#cancelar-solicitud-op').on('click', function(){
        $("#select-solicitante option[value='']").attr("selected",true);
        $("#select-empresa-solicitud option[value='']").attr("selected",true);
        $("#select-obra-solicitud option[value='']").attr("selected",true);
        $("#select-cuenta-solicitud option[value='']").attr("selected",true);
        $("#importe-solicitud-op").val("");
        $("#detalle-solicitud-op").val("");
        solicitante = '';
        empresa_sod = '';
        obra_sod    = '';
        cuenta_sod  = '';
        importe_sod = '';
        detalle_sod = '';
        location.reload();
        //window.location = "file_solicitud_op.php";        
    })

    $('#cancelar-solicitud-banco').on('click', function(){
        $("#select-solicitante option[value='']").attr("selected",true);
        $("#select-empresa-banco option[value='']").attr("selected",true);
        $("#select-obra-banco option[value='']").attr("selected",true);
        $("#select-cuenta-banco option[value='']").attr("selected",true);
        $("#importe-solicitud-banco").val("");
        $("#detalle-solicitud-banco").val("");
        solicitante = '';
        empresa_sod = '';
        obra_sod    = '';
        cuenta_sod  = '';
        recibe_banco = '';
        importe_sod = '';
        detalle_sod = '';
        location.reload();
        //window.location = "file_solicitud_banco.php";        
    })
    
    // Nueva Solicitud de orden de pago
    $('#nueva-solicitud').on('click', function(){
        location.reload();
        //window.location = "file_solicitud_op.php";      
    }) 


    // cerrar ventana exito orden de pago
    $('#cerrar-content-op').on('click',function(){
        $('#content').html("");
        $('#exito-op').fadeOut('fast');
        $("#select-empresa option[value='']").attr("selected",true);
        $("#select-obra option[value='']").attr("selected",true);
        $("#select-cuenta option[value='']").attr("selected",true);
        $("#importe-op").val("");
        $("#detalle-op").val("");
        empresa_sod = '';
        obra_sod    = '';
        cuenta_sod  = '';
        importe_sod = '';
        detalle_sod = '';    
    })


    /////////////////////////////

    // Retiros
    var persona = "";
    var concepto = "";
    var cuenta_rt = "";
    var importe_rt = parseInt(0);
    var detalle_rt = "";
    
    $("#select-persona-retiro").on('change', function(){
        persona = $("#select-persona-retiro").val();
        console.log('personal: '+persona)
    })

    $("#select-concepto-retiro").on('change', function(){
        concepto = $("#select-concepto-retiro").val();
        console.log(concepto)
    })

    $('#select-cuenta-retiro').on('change', function(){
        cuenta_rt = $('#select-cuenta-retiro').val();
        console.log(cuenta_rt)
    })

    $("#importe-retiro").keyup(function(){
        importe_rt = $('#importe-retiro').val();
        console.log(importe_rt)
    });

    $("#detalle-retiro").keyup(function(){
     detalle_rt = $('#detalle-retiro').val();
     console.log(detalle_rt)
    });        

    $('#aceptar-retiro').on('click', function(){                    
        if( (importe_rt > parseInt(0))  && (importe_rt !="") && (detalle_rt !="") && (cuenta_rt !="") && (concepto !="") && (persona !=""))
        {
            console.log('importe: '+importe_rt)
            console.log('detalle: '+detalle_rt)
            console.log('cuenta: '+cuenta_rt)
            $.post('retiros.php', {'persona': persona,'concepto': concepto,'cuenta':cuenta_rt, 'importe': importe_rt, 'detalle': detalle_rt}, (resp) =>{
                console.log(resp)
                if(resp = 'ok')
                { 
                    //EFECTO LOADING   
                    //Añadimos la imagen de carga en el contenedor
                    $('#content-retiro').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "POST",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-retiro').fadeIn(1000).html(data);
                            $('#exito-retiro').fadeIn('slow'); 
                            $("#select-persona-retiro option[value='']").attr("selected",true);
                            $("#select-concepto-retiro option[value='']").attr("selected",true);
                            $("#select-cuenta-retiro option[value='']").attr("selected",true);
                            $("#importe-retiro").val("");
                            $("#detalle-retiro").val("");
                            importe_rt = '';
                            detalle_rt = '';
                            persona = '';
                            concepto = '';
                            cuenta_rt = '';    
                        }
                    });
                    return false;
                                       
                }
                else
                { 
                    $("#select-persona-retiro option[value='']").attr("selected",true);
                    $("#select-concepto-retiro option[value='']").attr("selected",true);
                    $("#select-cuenta-retiro option[value='']").attr("selected",true);
                    $("#importe-retiro").val("");
                    $("#detalle-retiro").val("");
                    importe_rt = '';
                    detalle_rt = '';
                    persona = '';
                    concepto = '';
                    cuenta_rt = '';
                    alert('Error inesperado... intente nuevamente.');                   
                }
            });
        }
        else alert('Debe llenar todos los campos.');
        
    })

    //Cancelar Retiro
    $('#cancelar-retiro').on('click', function(){
        /*$("#select-persona-retiro option[value='']").attr("selected",true);
        $("#select-concepto-retiro option[value='']").attr("selected",true);
        $("#select-cuenta-retiro option[value='']").attr("selected",true);
        $("#importe-retiro").val("");
        $("#detalle-retiro").val("");
        importe_rt = "";
        detalle_rt = "";
        persona = "";
        concepto = "";
        cuenta_rt = "";*/
        window.location = "file_retiros.php"     
    }) 

    $('#nuevo-retiro').on('click', function(){
        window.location = "file_retiros.php";
    })

    // cerrar ventana exito retiro
    $('#cerrar-exito-retiro').on('click',function(){
        $('#content-retiro').html("");
        $("#select-persona-retiro option[value='']").attr("selected",true);
        $("#select-concepto-retiro option[value='']").attr("selected",true);
        $("#select-cuenta-retiro option[value='']").attr("selected",true);
        $("#importe-retiro").val("");
        $("#detalle-retiro").val("");
        importe_rt = '';
        detalle_rt = '';
        persona = '';
        concepto = '';
        cuenta_rt = '';  
        $('#content-retiro').fadeOut();
        $('#exito-retiro').fadeOut(); 
    })

    /*-----------------------------------------------------*/
    // transferencia
    var moneda = "";
    var caja_destino = "";
    var cantidad = parseInt(0);
    var observaciones = "";
    var numero_caja_destino ='';
    var cant_chqs = parseInt(0); 
    var tope;
    var datasend_tr = false; 

        $('#select-moneda').on('change', function() {   
            moneda = $(this).val();
            moneda_tr = moneda;
            console.log('moneda: '+moneda);
            if(moneda=='cheques') 
            {
                if($('.div-tabla-chq').is(':hidden'))
                {   
                    if($('.tabla-chq-transfer').find('tbody tr').length == parseInt(0))
                    {
                        $('.div-info-chq').slideDown(); // info no hay cheques
                        $('#cantidad-transfer').attr('readonly',true);
                    }
                    else {
                        console.log('filas tr-chq: '+$('tbody tr').length);
                        $('.div-tabla-chq').slideDown();  
                        $('#cantidad-transfer').attr('readonly',true);
                    }    
                }
                    
            }
            else{
                if($('.div-tabla-chq').is(':visible'))
                {
                    $('input[name=check_transfer]').each(function(){
                        if($(this).val() == '1'){
                            $(this).val('0');
                            $(this).prop("checked", false);
                        }
                    });
                    $('.div-tabla-chq').slideUp();
                    $('#cantidad-transfer').val("");
                    cantidad = parseInt(0);
                    $('#cantidad-transfer').attr('readonly',false);
                }
                else{
                    if($('.div-info-chq').is(':visible'))
                    {
                        $('.div-info-chq').slideUp(); // info no hay cheques
                        $('#cantidad-transfer').attr('readonly',false);
                    }
                }
                
            }

        })

    $('#select-caja-destino').on('change', function () {
        caja_destino = $(this).val();
        console.log('caja destino: ' + caja_destino);
        numero_caja_destino = parseInt($('#select-caja-destino option:selected').attr('caja'));  //lista_num_caja2[caja_destino];  Version anterior
        console.log('numero de caja destino: ' + numero_caja_destino)
           
            if(numero_caja_destino == parseInt(3))
                tope = parseInt(1);
            else    
                tope = parseInt(8); 
            console.log('tope: '+tope);         
        })

        $('#detalle-transfer').on('change', function() {
             observaciones = $('#detalle-transfer').val();
             console.log(observaciones)
        })

        $('#cantidad-transfer').on('change', function() {
            if(moneda == 'pesos' || moneda == 'cheques')
                cantidad = $('#cantidad-transfer').val();
            else cantidad = parseInt($('#cantidad-transfer').val());
            console.log('$/us$/€ '+cantidad)
        })

        var id_check_tr = parseInt(0);
        var lista_ids = [];
        
        $('input[name=check_transfer]').on('click', function(event){
            
            if(numero_caja_destino == parseInt(3))
            {
                tope = parseInt(1);  
            }
            else{
                tope = parseInt(8);
            }
            if($(this).val() == 0)
            {
                if(cant_chqs < tope)
                {
                    $(this).val('1'); // seleccionado
                    id_check_tr = $(this).prop('id'); 
                    var valor = $('.tabla-chq-transfer tr[id='+parseInt(id_check_tr)+']').find(".importe-chq").children().val();
                    lista_ids.push(id_check_tr); // lista de ids de cheques seleccionados
                    cant_chqs++
                    console.log('cantidad cheques: '+cant_chqs)
                    console.log(valor);
                    cantidad+=Number(valor);
                    
                    $('#cantidad-transfer').val(cantidad);
                }
                else{
                    if(numero_caja_destino == 3)
                    {
                        var info = "<strong>Solo puede depositar 1 cheque por operación</strong>";  
                    }
                    else
                    {
                        var info = "<strong>No puede transferir mas de 8 cheques.</strong>";
                    }
                        
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })
                    $(this).val('0'); // deseleccionado
                    $(this).prop("checked", false);
                }
                        
            }
            else{
                $(this).val('0'); // Deseleccionado
                id_check_tr = $(this).prop('id'); 
                var valor = $('.tabla-chq-transfer tr[id='+parseInt(id_check_tr)+']').find(".importe-chq").children().val();
                if(find_index(lista_ids,id_check_tr) >= parseInt(0))// verifico si existe el id en la lista                 
                 {
                    var j = find_index(lista_ids,id_check_tr);
                    lista_ids.splice(j, 1); // quito el id de la lista
                    cant_chqs--;
                 }
                console.log(valor);
                cantidad-=Number(valor);
                
                $('#cantidad-transfer').val(cantidad);
                
            }
            
        })

        $('#aceptar-transfer').on('click', function(){ 
            if(numero_caja_destino != parseInt(3))
            {
                if( (moneda !="") && (caja_destino != "") && (cantidad > parseInt(0)) && (cantidad !="") && (observaciones!="")) 
                {
                    if($('#exito-tr').is(':visible'))
                    {
                        $('#exito-tr').hide();
                        $('#content-transfer').show();
                    }
                    else{
                        $('#content-transfer').html("");
                        $('#content-transfer').show();
                    }

                    if(!datasend_tr)
                    {
                        datasend_tr = true;
                    }

                    $.post('transferencia.php',{'moneda':moneda, 'caja_destino':caja_destino,'numero_caja_destino': numero_caja_destino, 'cantidad':cantidad,'detalle':observaciones,'lista_ids':lista_ids}, (resp)=>{                 
                        console.log(resp)
                        if(resp = 'Transferencia realizada')
                        {
                            moneda ='';
                            caja_destino ='';
                            numero_caja_destino ='';
                            cantidad = parseInt(0);
                            observaciones ='';
                            lista_ids = [];
                            $('#content-transfer').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                            $.ajax({
                                type: "GET",
                                url: "sleep.php",
                                success: function(data) {
                                    //Cargamos finalmente el contenido deseado
                                    $('#content-transfer').fadeIn(1000).html(data);
                                    $('#exito-tr').slideDown();

                                    /*$('input[name=check_transfer]').each(function(){
                                        if($(this).val() == '1'){
                                            $(this).val('0');
                                            $(this).prop("checked", false);
                                        }
                                    });*/ 

                                    $('.tabla-chq-transfer tr').find("input[name=check_transfer]").each(function(){
                                        if($(this).val() == '1'){
                                            var fila = parseInt($(this).prop('id'));
                                            console.log('id tr: '+fila+' - valor: '+$(this).val());
                                            $('tr[id='+fila+']').remove();
                                            //carga y actualizacion exitosa.
                                        }
                                        
                                    })
                                
                                    if($('.div-tabla-chq').is(':visible'))
                                    {
                                        if($('.tabla-chq-transfer tbody tr').length == parseInt(0))
                                        {
                                            console.log('No hay filas.');
                                            $('.div-tabla-chq').slideUp();
                                        }
                                        else console.log('Todavia hay '+$('tbody tr').length+ ' filas.');
                                        
                                    }
                                    

                                    $('#select-moneda').val("");
                                    $('#select-caja-destino').val("")
                                    $('#detalle-transfer').val("");
                                    $('#cantidad-transfer').val(""); 
                                    moneda ='';
                                    caja_destino ='';
                                    numero_caja_destino ='';
                                    cantidad = parseInt(0);
                                    observaciones ='';
                                    lista_ids = [];
                                    cant_chqs = parseInt(1);
                                    datasend_tr = false;
                                }
                            });
                            return false;
                        
                        }
                        else 
                        {   
                            $('#select-moneda').val("");
                            $('#select-caja-destino').val("")
                            $('#detalle-transfer').val("");
                            $('#cantidad-transfer').val(""); 
                            moneda ='';
                            caja_destino ='';
                            numero_caja_destino ='';
                            cantidad = parseInt(0);
                            observaciones ='';
                            lista_ids = [];
                            cant_chqs = parseInt(1);
                            console.log(resp);
                            //alert(resp);
                            var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                            $('#modal-info').html(info);
                            $('#miModal').slideDown();

                            $('.close-modal').on('click', function(){
                                $('#miModal').slideUp();
                            })
                        }
                    });
                            
                    $('#cerrar-tr-pdf').on('click', function(){
                        $('#content-transfer').hide();
                        $('#exito-tr').slideUp();

                    })
                }
                else
                {
                    //alert('Debe llenar todos los campos');
                    var info = "<strong>Debe llenar todos los campos !</strong>";
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })
                    
                }
                
            } 
            else{
                if(cant_chqs == parseInt(1))
                {
                    if( (moneda !="") && (caja_destino != "") && (cantidad > parseInt(0)) && (cantidad !="") && (observaciones!="")) 
                    {
                        if($('#exito-tr').is(':visible'))
                        {
                            $('#exito-tr').hide();
                            $('#content-transfer').show();
                        }
                        else{
                            $('#content-transfer').html("");
                            $('#content-transfer').show();
                        }
    
                        if(!datasend_tr)
                        {
                            datasend_tr = true;
                        }
    
                        $.post('transferencia.php',{'moneda':moneda, 'caja_destino':caja_destino,'numero_caja_destino': numero_caja_destino, 'cantidad':cantidad,'detalle':observaciones,'lista_ids':lista_ids}, (resp)=>{                 
                            console.log(resp)
                            if(resp = 'Transferencia realizada')
                            {
                                moneda ='';
                                caja_destino ='';
                                numero_caja_destino ='';
                                cantidad = parseInt(0);
                                observaciones ='';
                                lista_ids = [];
                                $('#content-transfer').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                                $.ajax({
                                    type: "GET",
                                    url: "sleep.php",
                                    success: function(data) {
                                        //Cargamos finalmente el contenido deseado
                                        $('#content-transfer').fadeIn(1000).html(data);
                                        $('#exito-tr').slideDown();
    
                                        /*$('input[name=check_transfer]').each(function(){
                                            if($(this).val() == '1'){
                                                $(this).val('0');
                                                $(this).prop("checked", false);
                                            }
                                        });*/ 
    
                                        $('.tabla-chq-transfer tr').find("input[name=check_transfer]").each(function(){
                                            if($(this).val() == '1'){
                                                var fila = parseInt($(this).prop('id'));
                                                console.log('id tr: '+fila+' - valor: '+$(this).val());
                                                $('tr[id='+fila+']').remove();
                                                //carga y actualizacion exitosa.
                                            }
                                            
                                        })
                                    
                                        if($('.div-tabla-chq').is(':visible'))
                                        {
                                            if($('.tabla-chq-transfer tbody tr').length == parseInt(0))
                                            {
                                                console.log('No hay filas.');
                                                $('.div-tabla-chq').slideUp();
                                            }
                                            else console.log('Todavia hay '+$('tbody tr').length+ ' filas.');
                                            
                                        }
                                        
    
                                        $('#select-moneda').val("");
                                        $('#select-caja-destino').val("")
                                        $('#detalle-transfer').val("");
                                        $('#cantidad-transfer').val(""); 
                                        moneda ='';
                                        caja_destino ='';
                                        numero_caja_destino ='';
                                        cantidad = parseInt(0);
                                        observaciones ='';
                                        lista_ids = [];
                                        cant_chqs = parseInt(1);
                                        datasend_tr = false;
                                    }
                                });
                                return false;
                            
                            }
                            else 
                            {   
                                $('#select-moneda').val("");
                                $('#select-caja-destino').val("")
                                $('#detalle-transfer').val("");
                                $('#cantidad-transfer').val(""); 
                                moneda ='';
                                caja_destino ='';
                                numero_caja_destino ='';
                                cantidad = parseInt(0);
                                observaciones ='';
                                lista_ids = [];
                                cant_chqs = parseInt(1);
                                console.log(resp);
                                //alert(resp);
                                var info = "<strong>Error inesperado, intente nuevamente.</strong>";
                                $('#modal-info').html(info);
                                $('#miModal').slideDown();
    
                                $('.close-modal').on('click', function(){
                                    $('#miModal').slideUp();
                                })
                            }
                        });
                                
                        $('#cerrar-tr-pdf').on('click', function(){
                            $('#content-transfer').hide();
                            $('#exito-tr').slideUp();
    
                        })
                    } 
                    else
                    {
                        //alert('Debe llenar todos los campos');
                        var info = "<strong>Debe llenar todos los campos !</strong>";
                        $('#modal-info').html(info);
                        $('#miModal').slideDown();

                        $('.close-modal').on('click', function(){
                            $('#miModal').slideUp();
                        })
                        
                    }
                }
                else
                {
                    var info = "<strong>Solo puede depositar 1 cheque por operación</strong>";     
                    $('#modal-info').html(info);
                    $('#miModal').slideDown();

                    $('.close-modal').on('click', function(){
                        $('#miModal').slideUp();
                    })
                }
            }
        })
    
    $('#show-tr-pdf').on('click', function(){
        $('#content-transfer').hide();
        $('#exito-tr').hide();
    })
    

    $('#cancelar-transfer').on('click', function(){
        moneda ='';
        caja_destino ='';
        numero_caja_destino ='';
        cantidad = parseInt(0);
        observaciones ='';
        $('#select-moneda').val("");
        $('#select-caja-destino').val("")
        $('#detalle-transfer').val("");
        $('#cantidad-transfer').val("");
        $('#content-transfer').hide();
        $('#exito-tr').hide();
        if($('.div-info-chq').is(':visible'))
        {
            $('.div-info-chq').slideUp();
        }
        $('.tabla-chq-transfer tr').find("input[name=check_transfer]").each(function(){
            if($(this).val() == '1'){
                $(this).val('0');
                $(this).val()
                $(this).prop("checked", false);
                cant_chqs = parseInt(1); 
            }
            
        }) 
    })

    $('#nueva-transfer-check').on('click', function(){
        location.reload();
    })
    // transferencias realizadas
    $('#btn-cerrar-tr-pendientes').on('click', function(){
        $('#tr-pendentes').fadeOut('fast');
    })
    // transferencias realizadas
    $('#transferencia_rz').on('click', function(){
        $('#popup-compras').fadeOut('fast');
        if($('#tr-recibidas').is(':visible'))
        {
            $('#tr-recibidas').hide();
            $('#tr-realizadas').fadeIn('fast');
        }
        else $('#tr-realizadas').fadeIn('fast');

        $('#btn-cerrar-tr-realizadas').on('click', function(){
            $('#tr-realizadas').fadeOut('fast');
        });

    })
    // transferencias recibidas
    $('#transferencia_rd').on('click', function(){
        $('#popup-compras').fadeOut('fast');
        if($('#tr-recibidas').is(':hidden'))
            $('#tr-recibidas').fadeIn();
        
        $('#btn-cerrar-tr-recibidas').on('click', function(){
            $('#tr-recibidas').fadeOut('fast');
        });

    })
    ///////////////////////////////
    $('#operaciones').on('click', function(){
        $('#popup-compra-venta').fadeIn('fats');
    })

    //CANJES
    var moneda1='';
    var moneda2='';
    var cantidad_compra=0;
    var cantidad_resultado=0;
    var cotizacion=0;
    var detalle_canje='';
    $('#select-moneda-compra').on('change', function() {
        moneda1=$('#select-moneda-compra').val();
        console.log('moneda1: '+moneda1);
    })
    $('#select-moneda-pagar').on('change', function() {
        moneda2=$('#select-moneda-pagar').val();
        console.log('moneda2: '+moneda2);
    })
    $('#cantidad-compra').on('change', function() {
        cantidad_compra = $('#cantidad-compra').val();
        console.log('cantidad compra: '+cantidad_compra);
    })
    
    $('#detalle-canje').on('change', function() {
        detalle_canje=$('#detalle-canje').val();
        console.log('moneda1: '+detalle_canje);
    })

    $('#cotizacion').on('change', function() {
        cotizacion = $('#cotizacion').val();
        console.log('cotización: '+cotizacion)
    })
    $('#aceptar-compra').on('click', function(){
        if(moneda1!='' && moneda2!='' && cantidad_compra > parseInt(0) && cotizacion > parseInt(0) && detalle_canje!="")
        {
            if(moneda1 != moneda2)
            {
                if(moneda2 == 'dolares' || moneda2 == 'euros')
                    cantidad_resultado = ( cantidad_compra / cotizacion );
                else
                { 
                    cantidad_resultado = ( cantidad_compra * cotizacion );
                    console.log(cantidad_resultado);
                }
                
                if( $('#print-cange').is(':visible') )
                {
                    $('#print-cange').hide();
                    $('#content-compra').show();
                }
                else{
                    $('#content-compra').html("");
                    $('#content-compra').show();
                }

                $.post('canjes.php',{'moneda1':moneda1,'moneda2':moneda2,'cantidad_compra':cantidad_compra,'cotizacion':cotizacion,'detalle_canje':detalle_canje}, (resp)=>{
                    console.log('respuesa canje: '+resp);
                    if(parseInt(resp) == 1)
                    {
                        if(moneda2 == 'dolares')
                            $('#cantidad-resultado').val('US$ '+cantidad_resultado);
                        else
                            if(moneda2 =='euros')
                                $('#cantidad-resultado').val('€ '+cantidad_resultado);
                            else $('#cantidad-resultado').val('$ '+cantidad_resultado);

                        console.log('Compra de '+moneda1+ ' con exito !' );
                        //$('#content-compra').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                        $('#select-moneda-compra').val('');
                        $('#select-moneda-pagar').val('');
                        $('#cantidad-compra').val('');
                        $('#cotizacion').val('');
                        $('#cantidad-resultado').val('');
                        $('#detalle-canje').val('');
                        moneda1='';
                        moneda2='';
                        cantidad_compra=0;
                        cantidad_resultado=0;
                        cotizacion=0;
                        detalle_canje="";
                        $('#content-compra').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
            
                        $.ajax({
                            type: "GET",
                            url: "sleep.php",
                            success: function(data) {
                                //Cargamos finalmente el contenido deseado
                                $('#content-compra').fadeIn(1000).hide();
                                $('#print-cange').slideDown(); 
                                
                            }
                        });
                        return false;

                    }
                    else{
                        if(parseInt(resp) == 2){
                            //alert('No hay fondos suficientes para la compra');
                            var info = "<strong>No hay fondos suficientes.</strong>";
                            $('#modal-info').html(info);
                            $('#miModal').slideDown();

                            $('.close-modal').on('click', function(){
                                $('#miModal').slideUp();
                            })
                        }
                        else{
                            if(parseInt(resp) == 3){
                                //alert('Debe elegir la moneda pesos ($) para pagar');
                                var info = "<strong>Debe elegir la moneda pesos ($) para pagar</strong>";
                                $('#modal-info').html(info);
                                $('#miModal').slideDown();

                                $('.close-modal').on('click', function(){
                                    $('#miModal').slideUp();
                                })
                            }
                            
                        }
                    };
                }) 
            }
            else{
                var info = "<strong style>Igualdad de monedas, cambie e intente nuevamente.</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
            }
        }
        else{
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
        }
    })

    $('#cerrar-cange').on('click', function(){
        $('#print-cange').slideUp();
    })

    $('#btn-canje').on('click', function(){
        $('#print-cange').slideUp();

    })

    $('#simular-compra').on('click', function() {
        if(moneda1!='' && moneda2!='' && cantidad_compra > parseInt(0) && cotizacion > parseInt(0))
        {
            if(moneda1 != moneda2)
            {
                if(moneda2 == 'dolares' || moneda2 == 'euros')
                    cantidad_resultado = ( cantidad_compra / cotizacion );
                else
                { 
                    cantidad_resultado = ( cantidad_compra * cotizacion );
                    console.log(cantidad_resultado);
                }
                if(moneda2 == 'dolares')
                    $('#cantidad-resultado').val('US$ '+cantidad_resultado);
                else
                    if(moneda2 =='euros')
                        $('#cantidad-resultado').val('€ '+cantidad_resultado);
                    else $('#cantidad-resultado').val('$ '+cantidad_resultado); 
            }
            else{
                //alert('¡ Igualdad de monedas !');
                var info = "<strong>Igualdad de monedas, cambie e intente nuevamente.</strong>";
                $('#modal-info').html(info);
                $('#miModal').slideDown();

                $('.close-modal').on('click', function(){
                    $('#miModal').slideUp();
                })
            } 
        }
        else{
            //alert('¡ Campos vacios !');
            var info = "<strong>Debe llenar todos los campos !</strong>";
            $('#modal-info').html(info);
            $('#miModal').slideDown();

            $('.close-modal').on('click', function(){
                $('#miModal').slideUp();
            })
        } 
            
    })

    //CANCELAR COMPRA
    $('#cancelar-compra').on('click', function(){
        $('#select-moneda-compra').val('');
        $('#select-moneda-pagar').val('');
        $('#cantidad-compra').val('');
        $('#cotizacion').val('');
        $('#cantidad-resultado').val('');
        $('#detalle-canje').val('');
        moneda1='';
        moneda2='';
        cantidad_compra=0;
        cantidad_resultado=0;
        cotizacion=0;
        detalle_canje="";
        //window.location = 'file_compras.php';
    });
    
    

    /*-------------------------------------------------------*/
    //COMPRA DE CHEQUES
    var cantidad_cheq=0;
    var resultado_cheq=0;
    var detalle_cheq = "";

    $('#cantidad-cheq').on('change', function() {
        cantidad_cheq=$('#cantidad-cheq').val();
        console.log('cantidad: '+cantidad_cheq);
    })
    $('#resultado-cheq').on('change', function() {
        resultado_cheq=$('#resultado-cheq').val();
        console.log('resultado: '+resultado_cheq);
    })
    $('#detalle-cheq').on('change', function() {
        detalle_cheq = $('#detalle-cheq').val();
        console.log('detalle: '+detalle_cheq);
    })
    $('#aceptar-compra-cheq').on('click', function(){
        if(parseInt(cantidad_cheq)>0 && cantidad_cheq!="" && detalle_cheq!="")
        {   
            if( $('#print-canje-cheq').is(':visible') )
            {
                $('#print-canje-cheq').hide();
                $('#content-compra-cheq').show(); 
            }            
            $.post('compra_cheque.php',{'cantidad':cantidad_cheq,'detalle':detalle_cheq}, (resp)=>{
                console.log(resp);
                if(parseInt(resp) == 1)
                {
                       
                    $('#resultado-cheq').val('$ '+cantidad_cheq);

                    console.log('Compra de cheque  con exito !' );
                    $('#content-compra-cheq').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $('#cantidad-cheq').val('');
                    $('#resultado-cheq').val('');
                    $('#detalle-cheq').val('');
                    $('#ok').hide();     
                    cantidad_cheq=0;
                    resultado_cheq=0;                      
                    detalle_cheq="";
                    cantizacion=0;
                    //$('#content-compra-cheq').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
            
                    $.ajax({
                            type: "GET",
                            url: "sleep.php",
                            success: function(data) {
                                //Cargamos finalmente el contenido deseado
                                $('#content-compra-cheq').fadeIn(1000).hide();
                                $('#print-canje-cheq').slideDown(); 
                                $('#cerrar-print-cheq').on('click', function(){
                                    $('#print-canje-cheq').slideUp();
                                })
                            }
                    });
                    return false;

                }
                else {
                    alert('No hay fondos suficientes');
                    $('#cantidad-cheq').val('');
                    $('#resultado-cheq').val('');
                    $('#detalle-cheq').val('');
                    $('#ok').hide();     
                    cantidad_cheq=0;
                    resultado_cheq=0;                      
                    detalle_cheq="";
                    cantizacion=0;
                }
            }) 
            
        } 
        else alert('¡ Campos vacios !');
        
    })


    $('#simular-compra-cheq').on('click', function() {
        if(parseInt(cantidad_cheq)>0 && cantidad_cheq!="")
        {
            
            $('#resultado-cheq').val('$ '+cantidad_cheq);
            $('#ok').slideDown('slow'); 
        }
        else alert('Debe ingresar la cantidad del cheque.');
    })

    //CANCELAR COMPRA CHEQUE
    $('#cancelar-compra-cheq').on('click', function(){
        $('#cantidad-cheq').val('');
        $('#resultado-cheq').val('');
        $('#detalle-cheq').val('');
        $('#ok').hide();
        cantidad_cheq=0;
        resultado_cheq=0;
        detalle_cheq="";
        cantizacion=0;
    });

    
    /**-------------------------------------------------------- */
    //fondos ingresos
    var detalle = "";
    var ingreso = parseInt(0);
    var moneda_ingreso = "pesos";
    var simbolo = "$";
    $('#select-moneda-ingreso').on('change',function(){
        if($(this).val() == 'pesos'){
            moneda_ingreso = $(this).val();
            simbolo = '$';
            console.log('moneda: '+moneda_ingreso);
        }
        else
            if($(this).val() == 'dolares'){
                moneda_ingreso = $(this).val();
                simbolo = '$US';
                console.log('moneda: '+moneda_ingreso);
            }
            else{
                moneda_ingreso = $(this).val();
                simbolo = '€';
                console.log('moneda: '+moneda_ingreso);
            }
        
    })

    $('#detalle-ingreso').keyup(function(){
        detalle = $('#detalle-ingreso').val();
        console.log('detalle: '+detalle)
    })

    $('#importe-ingreso').keyup(function(){
        ingreso = $('#importe-ingreso').val();
        console.log(ingreso)
    })
    $('#aceptar-ingreso').on('click',function(){                            
        if(moneda_ingreso !='' && ingreso > parseInt(0) && ingreso != '' && detalle !='' )
        {
            if($('#print-ing').is(':visible')){
                $('#print-ing').hide();
                $('#content-ingreso').show();
            }
            $.post('ingreso.php', {'moneda':moneda_ingreso,'detalle': detalle, 'ingreso': ingreso}, (resp)=>{
                console.log(resp)
                console.log(ingreso+" "+moneda_ingreso+" ingresados con exitos")
                if(resp = 'ok')
                {
                    $('#form-ingreso').trigger("reset");
                    $('#importe-ingreso').val("");
                    $('#detalle-ingreso').val("");
                    
                    $('#content-ingreso').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            //$('#content-ingreso').fadeIn(1000).html(data+"<strong style='color: green;'> ingresó "+ simbolo+ingreso+ " con exito</strong>");
                            $('#content-ingreso').fadeIn(1000).hide();
                            $('#print-ing').slideDown();

                            $('#cerrar-print').on('click', function(){
                               $('#print-ing').slideUp(); 
                               $('#form-ingreso').trigger("reset");
                               $('#importe-ingreso').val("");
                               $('#detalle-ingreso').val("");
                               ingreso =parseInt(0);
                               detalle ='';
                            })

                            ingreso = parseInt(0);
                            detalle ='';  
                        }
                    });
                    return false;
                }
                else{
                    console.log(resp);
                    $('#form-ingreso').trigger("reset");
                    $('#importe-ingreso').val("");
                    $('#detalle-ingreso').val("");
                    ingreso =parseInt(0);
                    detalle ='';
                    $('#exito-ingreso').html("");
                    $('#exito-ingreso').fadeOut('fast');
                } 
            })
        }
        else{
            alert('Debe llenar todos los campos.');
            $('#form-ingreso').trigger("reset");
            $('#importe-ingreso').val("");
            $('#detalle-ingreso').val("");
            ingreso =parseInt(0);
            detalle ='';
        }
    })   

    $('#close-ingreso').on('click',function(){
        $('#form-ingreso').trigger("reset");
        $('#importe-ingreso').val("");
        $('#detalle-ingreso').val("");
        ingreso =parseInt(0);
        detalle ='';
    })

   /**-------------------------------------------------------- */
   
    //fondos Egresos  
    var detalle = "";
    var egreso = parseInt(0);
    var moneda_egreso = "pesos";
    var simbolo = "$";
    $('#select-moneda-egreso').on('change',function(){

        if($(this).val() == "pesos"){
            moneda_egreso = $(this).val();
            simbolo = '$';
            console.log('moneda: '+moneda_egreso);
        }
        else
            if($(this).val() == "dolares"){
                moneda_egreso = $(this).val();
                simbolo = '$US';
                console.log('moneda: '+moneda_egreso);
            }
            else{
                moneda_egreso = $(this).val();
                simbolo = '€';
                console.log('moneda: '+moneda_egreso);
            }
        
    })

    $('#detalle-egreso').keyup(function(){
        detalle = $('#detalle-egreso').val();
        console.log('detalle: '+detalle)
    })

    $('#importe-egreso').keyup(function(){
        egreso = $('#importe-egreso').val();
        console.log(egreso)
    })


    $('#aceptar-egreso').on('click',function(){                            
        if(moneda_egreso !='' && egreso > parseInt(0) && egreso !='' && detalle !='' )
        {
            
            if($('#print-egre').is(':visible')){
                $('#print-egre').hide();
                $('#content-egreso').show();
            }
            $.post('egreso.php', {'moneda':moneda_egreso,'detalle': detalle, 'egreso': egreso}, (res)=>{
                console.log(res);
                console.log(egreso+" "+moneda_egreso+" EGRESADOS con exitos");
                if(res = 'ok')
                {
                    $('#form-egreso').trigger("reset");
                    $('#importe-egreso').val("");
                    $('#detalle-egreso').val("");
                    
                    $('#content-egreso').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            //$('#content-ingreso').fadeIn(1000).html(data+"<strong style='color: green;'> ingresó "+ simbolo+ingreso+ " con exito</strong>");
                            $('#content-egreso').fadeIn(1000).hide();
                            $('#print-egre').slideDown();

                            $('#cerrar-print-egre').on('click', function(){
                               $('#print-egre').slideUp(); 
                               $('#form-egreso').trigger("reset");
                               $('#importe-egreso').val("");
                               $('#detalle-egreso').val("");
                               egreso =parseInt(0);
                               detalle ='';
                            })

                            ingreso = parseInt(0);
                            detalle ='';  
                        }
                    });
                    return false;
                }

                else{
                    console.log(res);
                    $('#form-egreso').trigger("reset");
                    $('#importe-egreso').val("");
                    $('#detalle-egreso').val("");
                    ingreso =parseInt(0);
                    detalle ='';
                    $('#exito-egreso').html("");
                    $('#exito-egreso').fadeOut('fast');
                } 
            }) 
        }
        else{
            alert('Debe llenar todos los campos.');
            $('#form-egreso').trigger("reset");
            $('#importe-egreso').val("");
            $('#detalle-egreso').val("");
            ingreso =parseInt(0);
            detalle ='';
        }
    })   

    $('#close-egreso').on('click',function(){
        $('#form-egreso').trigger("reset");
        $('#importe-egreso').val("");
        $('#detalle-egreso').val("");
        ingreso =parseInt(0);
        detalle ='';
    })

    /**------------------------------------------------------------- */
        
    //Eliminar fila de caja
    var fila = parseInt(0);
    var elem;
    $('.borrar').on('click', function(event){
        event.preventDefault();
        fila = $(this).attr('id'); 
        //elem = $(this);
        var i = $(this).closest('tr');
        var indice = $('table #tbody-datos tr').index(i) ;

        console.log('indice: '+indice)

        var info = "<strong>¿Realmente desea eliminar el movimiento "+$(this).attr('id')+"? </strong>";
        $('#modal-info').html(info);
        $('#miModal').slideDown();

        $('.ok-modal-delete').on('click', function(){
            $('#miModal').slideUp();
            //elem.closest('tr').remove();  original
            
             var valor = $("#tbody-datos tr:eq("+indice+") td:eq(0)").text();  
             console.log('valor: '+valor)  

             var vcampo = "<s style='color: #C70039;'>"+valor+"</s>";
             $("#tbody-datos tr:eq("+indice+") td:eq(0)").html(vcampo);
            
            $.post('eliminar.php', {'fila': fila,}, resp =>{
                console.log("Respuesta: "+resp)
                if(resp !='Error')
                {
                    console.log('Fila '+fila+' eliminada');
                }
                else console.log('No se eliminó la fila '+fila+' en la BD');
              
            })
        })

        $('.close-modal-delete').on('click', function(){
            $('#miModal').slideUp();
        })

        /* --------------------- */
        //ELIMINAR FILA - VERSION ORIGINAL
        /*var fila = parseInt(0);
        $('.borrar').on('click', function(event){
            event.preventDefault();
            console.log('fila a eliminar: '+$(this).attr('id'))

            if(confirm('¿ Realmente desea eliminar el movimiento '+ $(this).attr('id')+ '?'))
            {
                $(this).closest('tr').remove();            
                fila = $(this).attr('id'); 
                
                $.post('eliminar.php', {'fila': fila,}, resp =>{
                    console.log(resp)
                    if(resp !='Error')
                    {
                        console.log('Fila '+fila+' eliminada');
                    }
                    else console.log('No se eliminó la fila '+fila+' en la BD');
                
                })
            }
        })*/
        /* --------------------- */
    })


    /**---------------------------------------------------- */
    // CAMBIAR CONTRASEÑA
    var pass1 = "";
    var pass2 = "";

    $('#pass1').on('change', function(){
        pass1 = $('#pass1').val();
    })

    $('#pass2').on('change', function(){
        pass2 = $('#pass2').val();
    })

    $('#cambiar-pass').on('click', function(){
        if( pass1 == pass2 && pass1 !="" && pass2 !="")
        {
            $.post('cambiar_password.php', {'password': pass1}, resp => {
                if(resp == 'ok')
                {
                    $('#content-pass').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-pass').fadeIn(1000).html(data+"<strong style='color: green;'> contraseña actualizada.</strong>");
                            pass1 = "";
                            pass2 = ""; 
                            $('#pass1').val("");
                            $('#pass1').val(""); 
                        }
                    });
                    return false;
                }
                else alert('Error inesperado... Intente nuevamente.')

            })
        }
        else alert('¡ Las contraseñas no coinciden !');
    })

    // cancelar contraseña
    $('#cancelar-pass').on('click', function(){
        pass1 = "";
        pass2 = ""; 
        $('#pass1').val("");
        $('#pass1').val("");
    })

    /**-------------------------------------------------------- */
    // Reimprimir orden de pago
    var id = parseInt(0);   
    $('.btn-duplicate-op').on('click', function(){
        id = $(this).attr('id');     
        $.post('orden_pago_temp.php', {'id': id}, resp =>{      
            window.open('factura/reimprimir_op_pdf.php', '_blank');
            return false;              
        })
    })
   
    /**--------------------------------------------------- */
    //Agregar Empresa
   var nombre_empresa = "";
   $('#nombre-empresa').on('change', function(){
        nombre_empresa = $('#nombre-empresa').val();
        console.log(nombre_empresa);
    })
    $('#agregar-empresa').on('click', function(){
        if(nombre_empresa != "")
        {
            $.post('agregar_empresa.php', {'nombre_empresa': nombre_empresa}, resp=>{
                if(resp == "ok")
                {
                    console.log(resp);
                    $('#content-nueva-empresa').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-nueva-empresa').fadeIn(1000).html(data+"<strong style='color: green;'> Empresa creada con exito.</strong>");
                            nombre_obra = "";
                            $('#nombre-empresa').val("");
                        }
                    });
                    return false;
                }
                else alert('Error inesperado... intente nuevamente.');
            })
        }
        else
        {
            alert('¡ Campo vacio !'); 
            nombre_obra = "";
            $('#nombre-empresa').val("");
        }
        
   }) 

   /**-------------------------------------------------- */
   // Editar una Empresa

   $('.editar-empresa').on('click', function(){

        var id_empresa = $(this).prop('id');

        var desc = $('#nueva_desc_em').val(); 

        $.post('editar_empresa.php', {'id_empresa': id_empresa, 'desc': desc}, resp =>{
            console.log(resp);
            if(resp == 'ok'){

                $('#content-edicion-empresa').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                //$('#result-cuenta').fadeOut();
                $.ajax({
                    type: "GET",
                    url: "sleep.php",
                    success: function(data) {
                        //Cargamos finalmente el contenido deseado
                        
                        $('#content-edicion-empresa').fadeIn(1000).html(data+"<strong style='color: green;'> Edición realizada con exito.</strong>");
                        
                    }
                });
                return false;
            }
            else alert('Error al editar cuenta.');
        })  
    })


    // Cancelar edicion de cuenta
    $('#cancelar-edicion-empresa').on('click',function(){
        window.location="file_editar_empresa.php";
    })


   /*--------------------------------------------------*/ 

   //Agregar cuenta contable
   var nombre_cuenta = "";
   $('#nombre-cuenta').on('change', function(){
        nombre_cuenta = $('#nombre-cuenta').val();
        console.log(nombre_cuenta);
    })

    $('#agregar-cuenta').on('click', function(){
        if(nombre_cuenta != "")
        {
            $.post('agregar_cuenta.php', {'nombre_cuenta': nombre_cuenta}, resp=>{
                if(resp == "ok")
                {
                    console.log(resp);
                    $('#content-cuenta-nueva').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-cuenta-nueva').fadeIn(1000).html(data+"<strong style='color: green;'> Cuenta creada con exito.</strong>");
                            nombre_cuenta = "";
                            $('#nombre-cuenta').val("");
                        }
                    });
                    return false;
                }
                else alert('Error inesperado... intente nuevamente.');
            })
        }
        else
        {
            alert('¡ Campo vacio !'); 
            nombre_cuenta = "";
            $('#nombre-cuenta').val("");
        }
        
    })

    /*--------------------------------------------------- */
    // Editar Cuenta Contale

    $('.editar-cuenta').on('click', function(){

        var codigo = $(this).prop('id');

        var desc = $('#nueva_desc').val(); 

        $.post('editar_cuenta.php', {'codigo': codigo, 'desc': desc}, resp =>{
            console.log(resp);
            if(resp == 'ok'){

                $('#content-edicion').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                //$('#result-cuenta').fadeOut();
                $.ajax({
                    type: "GET",
                    url: "sleep.php",
                    success: function(data) {
                        //Cargamos finalmente el contenido deseado
                        
                        $('#content-edicion').fadeIn(1000).html(data+"<strong style='color: green;'> Edición realizada con exito.</strong>");
                        
                    }
                });
                return false;
            }
            else alert('Error al editar cuenta.');
        })  
    })

    // Cancelar edicion de cuenta
    $('#cancelar-edicion').on('click',function(){
        window.location="file_editar_cuenta.php";
    })


    /*-------------------------------------------------- */
   //Agregar Obra
   var nombre_obra = "";
   $('#nombre-obra').on('change', function(){
        nombre_obra = $('#nombre-obra').val();
        console.log(nombre_obra);
    })

   $('#agregar-obra').on('click', function(){
        if(nombre_obra != "")
        {
            $.post('agregar_obra.php', {'nombre_obra': nombre_obra}, resp=>{
                if(resp == "ok")
                {
                    console.log(resp);
                    $('#content-obra-nueva').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                    $.ajax({
                        type: "GET",
                        url: "sleep.php",
                        success: function(data) {
                            //Cargamos finalmente el contenido deseado
                            $('#content-obra-nueva').fadeIn(1000).html(data+"<strong style='color: green;'> Obra creada con exito.</strong>");
                            nombre_obra = "";
                            $('#nombre-obra').val("");
                        }
                    });
                    return false;
                }
                else alert('Error inesperado... intente nuevamente.');
            })
        }
        else
        {
            alert('¡ Campo vacio !'); 
            nombre_obra = "";
            $('#nombre-obra').val("");
        }
        
   }) 

   /*-------------------------------------------------------- */
    // Editar una Obra

    $('.editar-obra').on('click', function(){

        var id = $(this).prop('id');

        var desc = $('#nueva_desc_obra').val(); 

        $.post('editar_obra.php', {'id_obra': id, 'desc': desc}, resp =>{
            console.log(resp);
            if(resp == 'ok'){

                $('#content-edicion-obra').html('<div class="loading"><img src="img/loader.gif"/><br/>Un momento, por favor...</div>');
                //$('#result-cuenta').fadeOut();
                $.ajax({
                    type: "GET",
                    url: "sleep.php",
                    success: function(data) {
                        //Cargamos finalmente el contenido deseado
                        
                        $('#content-edicion-obra').fadeIn(1000).html(data+"<strong style='color: green;'> Edición realizada con exito.</strong>");
                        
                    }
                });
                return false;
            }
            else alert('Error al editar obra.');
        })  
    })


    // Cancelar edicion de cuenta
    $('#cancelar-edicion-obra').on('click',function(){
        window.location="file_editar_obra.php";
    })



    /*------------------------------------------------- */
    // Caragr cobranza //
    

    /**----------------------------------------------- */

    // Eliminar solicitud de orden de pago
    var num_orden = parseInt(0);
    var id_tr = parseInt(0);
    $('.btn-delete-sop').on('click', function(){
        
        console.log(lista_ids_chq_cart);
        num_orden = $(this).attr('id');
        id_tr = parseInt(num_orden)+parseInt(1);
        if(confirm('¿ Desea Eliminar la solicitud Nº '+num_orden+' '+ '?')){
            
            $.post('eliminar_solicitud_op.php', {'num_orden': num_orden}, resp =>{
                console.log('Resp eliminar solicitud: '+resp)
                if(parseInt(resp) == 1){
                    $(this).closest('tr').remove();
                    $('tr[id='+(id_tr)+']').remove();
                    //console.log($('tr[id='+(id_tr)+']').parent());
                }
            })
              
        }
              
    })
    
    /*------------------------------------------------*/
    // NUEVA CARGA DE SERVICIOS
    var id =parseInt(0);
    var i =parseInt(0);
    var j = parseInt(0);
    var c = parseInt(0);
    var nom_cliente="";
    var telefono="";
    var loteo="";
    var lote="";
    lista = [];
    $('#input-nom-cliente').on('change', function(){
        nom_cliente = $('#input-nom-cliente').val();
        console.log('Nombre: '+nom_cliente);
    })
    $('#input-telefono').on('change', function(){
        telefono = $('#input-telefono').val();
        console.log('Telefono: '+telefono);
    })
    $('#select-loteo').on('change', function(){
        loteo = $('#select-loteo').val();
        console.log('loteo: '+loteo);
    })

    $('#input-lote').on('change', function(){
        lote = $('#input-lote').val();
        console.log('Lote: '+lote);

        digitos = parseInt(lote.substring(2,(parseInt(lote.length))));
        cant_digitos = lote.substring(2,(parseInt(lote.length))).length;

    })

    // Realizar carga de servicios
    $('.load-serv').on('click', function(){
        if(nom_cliente!="" && telefono!="" && loteo!="" && lote!="")
        {
            if(lote.length == parseInt(6))
            {
                // consulta de lote existente
                $.post('cheq_lote.php', {'lote':lote}, r =>{
                    if(r == true)
                    {
                        //codigo de la carga
                        if(confirm('¿Cargar datos?'))
                        {
                            var cant_filas = parseInt($("#mi-tabla tr").length)-parseInt(1);
                                    
                            id = $(this).attr('id');

                            lista1 = [];
                            lista2 = [];

                            lista1.push(nom_cliente);
                            lista1.push(telefono);
                            lista1.push(loteo);
                            lista1.push(lote);
                                
                            // clic en boton cargar de una fila
                            //$(this).parents('tr[id='+id+']').find(".valor").each(function() {
                            for(i = parseInt(1); i<=cant_filas; i++)
                            {
                                $('tr[id='+i+']').find(".valor").each(function() {
                                    lista2.push($(this).children().val());
                                });
                            }
                            console.log(lista2);

                            $.post('cargar_servicio.php',{'datos_cliente':JSON.stringify(lista1),'datos_servicios':JSON.stringify(lista2)}, resp =>{
                                console.log('Respuesta servicio: '+resp)
                                if(parseInt(resp) == parseInt(1))
                                {
                                    $('#info-load-service').html('<div class="loading"><img src="img/loader.gif"/>Un momento, por favor...</div>');
                                    $.ajax({
                                        type: "GET",
                                        url: "sleep.php",
                                        success: function(data) 
                                        {
                                            $('#info-load-service').fadeIn(1000).html("<strong style='color: green;'> Servicios cargados con exito!</strong>");          
                                            $('#input-nom-cliente').val("");
                                            $('#input-telefono').val("");
                                            $('#select-loteo').val("");
                                            $('#input-lote').val("");

                                            for(i = parseInt(1); i<=cant_filas; i++)
                                            {
                                                $('tr[id='+i+']').find("#input-recibo").val("");
                                                $('tr[id='+i+']').find('#input-fecha-solicitud').val("");
                                                $('tr[id='+i+']').find('#select-load-estado').val("");
                                                $('tr[id='+i+']').find('#select-forma-pago').val("");
                                            }
                                                
                                                    
                                        }
                                    });
                                    return false;
                                }
                                else
                                {
                                    alert('Error al cargar servicio.');
                                    $('#input-nom-cliente').val("");
                                    $('#input-telefono').val("");
                                    $('#select-loteo').val("");
                                    $('#input-lote').val("");

                                    for(i = parseInt(1); i<=cant_filas; i++)
                                    {
                                        $('tr[id='+i+']').find("#input-recibo").val("");
                                        $('tr[id='+i+']').find('#input-fecha-solicitud').val("");
                                        $('tr[id='+i+']').find('#select-load-estado').val("");
                                        $('tr[id='+i+']').find('#select-forma-pago').val("");
                                    }
                                }
                            })
                        }
                    }
                    else alert('Ya existe el lote '+''+lote+'');
                    
                })
                                  
            }
            else{
                alert('Formato de lote incorrecto... ej : BC1234, bc1234, bc0123 (6 caracteres)');
                
            }
        }
        else{
            alert('¡ Campos Vacios !');
            $('#input-nom-cliente').val("");
            $('#input-telefono').val("");
            $('#select-loteo').val("");
            $('#input-lote').val("");

            for(i = parseInt(1); i<=cant_filas; i++)
            {
                $('tr[id='+i+']').find("#input-recibo").val("");
                $('tr[id='+i+']').find('#input-fecha-solicitud').val("");
                $('tr[id='+i+']').find('#select-load-estado').val("");
                $('tr[id='+i+']').find('#select-forma-pago').val("");
            }
        }
    })

    // Cancelar carga de servicio    
    $('.cancel-serv').on('click', function(){
        window.location = 'file_load_service.php';
    })

    /*------------------------------------------------*/

    // Autorizar solicitud orden de pago
   $('.btn-autorizar-sop').on('click', function(){
       
        var num = $(this).prop('id');

        $.post('autorizar_sop.php', {'num': num}, (resp)=>{
            console.log(resp)
            
            $(this).html("<img src='img/loader.gif' style='width: 15px; height; 15px; color: withe;'/>");
            setTimeout(function() {
                $( "button[id="+num+"]" ).hide();
            }, 1000);

            setTimeout(function() {   
                $('#'+num).html("<span style='color: #107D23';>Autorizada</span>");    
            }, 1000);
            
        })

    
    })
    
    /*--------------------------------------------------- */
    
    // Emitir orden de pago (antes era solicitud) 
    var id_orden2=parseInt(0);
    var id_tr2=parseInt(0);
    $('.btn-solicitud-sop').on('click', function(){
       
        id_orden2 = $(this).prop('id');
        console.log('btn solicitud: '+id_orden2);
        id_tr2 = parseInt(id_orden2)+parseInt(1);
               
        $(this).closest('tr').remove();
        $('tr[id='+(id_tr2)+']').remove();
                               
    })

    // Realizar servicio (version 1)
    
    $('.link-realizar').on('click', function(){
        var id_link = $(this).prop('id');
        var id_fr   = parseInt($(this).prop('id')) + parseInt(1);

        $.post('actualizar_estado_lote.php',{'id_servicio':id_link}, resp =>{
            if(resp = 'state ok')
            {
                $(this).fadeOut(900);
                $('td[id='+id_link+']').css("background" ,"#91EC7F");
                $('td[id='+id_link+']').html("Realizado");

                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();
                var year = d.getFullYear();
                var output = day + '/' + month + '/' + year.toString().substring(2);
                  
                $('td[fr_id='+id_link+']').html(output);
            }
            else{
                alert('Error inesperado...Intente nuevamente');
            }
        })
    })
    

    // Realizar servicio (version 2)
    /*$('.link-realizar').on('click', function(){
        var id_link = $(this).prop('id');
        var servicio = $(this).attr('servicio');
        console.log('Servicio: '+servicio)
        $.post('actualizar_estado_lote.php',{'servicio':servicio,'id_servicio':id_link}, resp =>{
            if(resp = 'state ok')
            {
                $(this).fadeOut(900);
                $('td[id='+id_link+']').css("background" ,"#91EC7F");
                $('td[id='+id_link+']').html("Realizado");

                var d = new Date();
                var month = d.getMonth()+1;
                var day = d.getDate();
                var year = d.getFullYear();
                var output = day + '/' + month + '/' + year.toString().substring(2);
                  
                $('td[fr_id='+id_link+']').html(output);
            }
            else{
                alert('Error inesperado...Intente nuevamente');
            }
        })
    })*/

    
   

}); //end scripts


