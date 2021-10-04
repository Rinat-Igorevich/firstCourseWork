'use strict';

const toggleHidden = (...fields) => {

  fields.forEach((field) => {

    if (field.hidden === true) {

      field.hidden = false;

    } else {

      field.hidden = true;

    }
  });
};

const labelHidden = (form) => {

  form.addEventListener('focusout', (evt) => {

    const field = evt.target;
    const label = field.nextElementSibling;

    if (field.tagName === 'INPUT' && field.value && label) {

      label.hidden = true;

    } else if (label) {

      label.hidden = false;

    }
  });
};

const toggleDelivery = (elem) => {

  const delivery = elem.querySelector('.js-radio');
  const deliveryYes = elem.querySelector('.shop-page__delivery--yes');
  const deliveryNo = elem.querySelector('.shop-page__delivery--no');
  const fields = deliveryYes.querySelectorAll('.custom-form__input');

  delivery.addEventListener('change', (evt) => {

    if (evt.target.id === 'dev-no') {

      fields.forEach(inp => {
        if (inp.required === true) {
          inp.required = false;
        }
      });


      toggleHidden(deliveryYes, deliveryNo);

      deliveryNo.classList.add('fade');
      setTimeout(() => {
        deliveryNo.classList.remove('fade');
      }, 1000);

    } else {

      fields.forEach(inp => {
        if (inp.required === false) {
          inp.required = true;
        }
      });

      toggleHidden(deliveryYes, deliveryNo);

      deliveryYes.classList.add('fade');
      setTimeout(() => {
        deliveryYes.classList.remove('fade');
      }, 1000);
    }
  });
};

const filterWrapper = document.querySelector('.filter__list');
if (filterWrapper) {

  filterWrapper.addEventListener('click', evt => {

    const filterList = filterWrapper.querySelectorAll('.filter__list-item');

    filterList.forEach(filter => {

      if (filter.classList.contains('active')) {

        filter.classList.remove('active');

      }

    });

    const filter = evt.target;

    filter.classList.add('active');

  });

}

const shopList = document.querySelector('.shop__list');
if (shopList) {

  shopList.addEventListener('click', (evt) => {

    const prod = evt.path || (evt.composedPath && evt.composedPath());

    if (prod.some(pathItem => pathItem.classList && pathItem.classList.contains('shop__item'))) {

      const shopOrder = document.querySelector('.shop-page__order');

      toggleHidden(document.querySelector('.intro'), document.querySelector('.shop'), shopOrder);

      window.scroll(0, 0);

      shopOrder.classList.add('fade');
      setTimeout(() => shopOrder.classList.remove('fade'), 1000);

      const form = shopOrder.querySelector('.custom-form');

      labelHidden(form);

      toggleDelivery(shopOrder);

      const buttonOrder = shopOrder.querySelector('.button');
      const popupEnd = document.querySelector('.shop-page__popup-end');

      buttonOrder.addEventListener('click', (evt) => {

        form.noValidate = true;

        const inputs = Array.from(shopOrder.querySelectorAll('[required]'));

        inputs.forEach(inp => {
          if (!!inp.value) {
            if (inp.classList.contains('custom-form__input--error')) {
              inp.classList.remove('custom-form__input--error');
            }
          } else {
            inp.classList.add('custom-form__input--error');
          }
        });

        createOrder(evt, inputs, form)

      });

      function succeess(inputs) {
        if (inputs.every(inp => !!inp.value)) {
          inputs.forEach(element => console.log(element.id + element.value))

          evt.preventDefault();

          toggleHidden(shopOrder, popupEnd);

          popupEnd.classList.add('fade');
          setTimeout(() => popupEnd.classList.remove('fade'), 1000);

          window.scroll(0, 0);

          const buttonEnd = popupEnd.querySelector('.button');

          buttonEnd.addEventListener('click', () => {

            popupEnd.classList.add('fade-reverse');

            setTimeout(() => {

              popupEnd.classList.remove('fade-reverse');

              toggleHidden(popupEnd, document.querySelector('.intro'), document.querySelector('.shop'));

            }, 1000);
            form.reset()

          });
        } else {
            window.scroll(0, 0);
            evt.preventDefault();
        }
      }

      function createOrder(evt, inputs, form) {

        evt.stopPropagation();
        evt.preventDefault()
        let action = 'create_order'
        let productID = prod[0].id
        let surname = $('#surname').val()
        let name = $('#name').val()
        let phone = $('#phone').val()
        let email = $('#email').val()
        let thirdName = $('#thirdName').val()


        let delivery = 'pickup'
        let city = null
        let street = null
        let home = null
        let aprt = null

        if (!$('#dev-no').prop('checked')){
          delivery = 'courier'
          city = $('#city').val()
          street = $('#street').val()
          home = $('#home').val()
          aprt = $('#aprt').val()
        }

        let payment = $('#cash').prop('checked') ? 'cash' : 'card'
        let comment = $('#comment').val()


        $.ajax({
          url : '/index.php',
          method : 'POST',
          cache : false,

          dataType : 'json',
          data : {
            action:action,
            productID:productID,
            surname:surname,
            name:name,
            phone:phone,
            customerEmail:email,
            thirdName:thirdName,
            delivery:delivery,
            city:city,
            street:street,
            home:home,
            aprt:aprt,
            payment:payment,
            comment:comment
          },
          success : function (respond, status, jqXHR){
            if(typeof respond.error === 'undefined' ){
              succeess(inputs)
            } else {
              window.scroll(0, 0);
              alert(respond.error)
              console.log(respond.error)
            }

          },
          error: function(jqXHR, status, errorThrown){
            console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
          }
        })
      }
    }
  });
}

const pageOrderList = document.querySelector('.page-order__list');
if (pageOrderList) {

  pageOrderList.addEventListener('click', evt => {

    if (evt.target.classList && evt.target.classList.contains('order-item__toggle')) {
      var path = evt.path || (evt.composedPath && evt.composedPath());
      Array.from(path).forEach(element => {
        if (element.classList && element.classList.contains('page-order__item')) {
          element.classList.toggle('order-item--active');
        }
      });
      evt.target.classList.toggle('order-item__toggle--active');
    }

    if (evt.target.classList && evt.target.classList.contains('order-item__btn')) {

      change_order_status(evt)

      const status = evt.target.previousElementSibling;

      if (status.classList && status.classList.contains('order-item__info--no')) {
        status.textContent = 'Выполнено';
      } else {
        status.textContent = 'Не выполнено';
      }

      status.classList.toggle('order-item__info--no');
      status.classList.toggle('order-item__info--yes');

    }

  });

  function change_order_status(evt) {
    let orderID = evt.target.id
    let orderStatus = evt.target.value

    $.ajax({
      url: '/index.php',
      method: 'POST',
      cache: false,
      dataType: 'json',
      data: {action: 'change_order_status', orderID: orderID, orderStatus:orderStatus},
      success: function (respond, status, jqXHR) {
        if (typeof respond.error === 'undefined') {
          evt.target.value = respond.result
        } else {

        }
      },
      error: function (jqXHR, status, errorThrown) {
        console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
      }
    })
  }
}

const checkList = (list, btn) => {

  if (list.children.length === 1) {

    btn.hidden = false;

  } else {
    btn.hidden = true;
  }

};

const addList = document.querySelector('.add-list');
if (addList) {

  const form = document.querySelector('.custom-form');
  labelHidden(form);

  const addButton = addList.querySelector('.add-list__item--add');
  const addInput = addList.querySelector('#product-photo');

  checkList(addList, addButton);

  addInput.addEventListener('change', evt => {

    const template = document.createElement('LI');
    const img = document.createElement('IMG');

    template.className = 'add-list__item add-list__item--active';
    template.addEventListener('click', evt => {
      addList.removeChild(evt.target);
      addInput.value = '';
      checkList(addList, addButton);
    });

    const file = evt.target.files[0];

    const reader = new FileReader();

    reader.onload = (evt) => {
      img.src = evt.target.result;
      template.appendChild(img);
      addList.appendChild(template);
      checkList(addList, addButton);
    };

    reader.readAsDataURL(file);

  });

  let files;
  $('input[type=file]').on('change', function (){
    files = this.files;

  });

  const button = document.querySelector('.button');
  const popupEnd = document.querySelector('.page-add__popup-end');

  button.addEventListener('click', (evt) => {
    let s = function if_success(){
      evt.preventDefault();

      form.hidden = true;
      popupEnd.hidden = false;
    }
    changeProduct(evt, s);

  })
  function changeProduct(event, s) {

    event.preventDefault()
    event.stopPropagation();
    let url = new URL(window.location.href)

    let data = new FormData();

    if(typeof files != 'undefined'){
      $.each (files, function (key, value){
        data.append(key, value);
      });
    }
    let categories = []
    categories = $('.custom-form__select').val()

    if ($('#new').prop('checked') === true){
      data.append('isNew', 'true')
    }else {
      data.append('isNew', 'false')
    }
    let product_ID = null

    if (!(url.searchParams.get('product') === null)){
      product_ID = url.searchParams.get('product')
      data.append('productID', product_ID)
    }

    if ($('#sale').prop('checked') === true){
      data.append('isSale', 'true')
    }else {
      data.append('isSale', 'false')
    }
    if (event.target.value === 'addProduct'){
      data.append('action', 'add_product');
    } else if (event.target.value === 'changeProduct'){
      data.append('action', 'change_product');

    }
    data.append('productName', $('#product-name').val());
    data.append('productPrice', $('#product-price').val());
    data.append('categories', categories);

    $.ajax({
      url : '/index.php',
      method : 'POST',
      cache : false,

      dataType : 'json',
      data : data,
      processData: false,
      contentType: false,

      success : function (respond, status, jqXHR){
        if(typeof respond.error === 'undefined' ){
          console.log(respond.result)
          s()
        } else {
          console.log(respond.error)
        }
      },
      error: function(jqXHR, status, errorThrown){
        console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
      }
    })
  }
}

const productsList = document.querySelector('.page-products__list');
if (productsList) {

  productsList.addEventListener('click', evt => {

    const target = evt.target;

    if (target.classList && target.classList.contains('product-item__delete')) {
      target.parentElement.remove()
      deleteProduct(target.value)
    }
  });
  function deleteProduct(product_id) {
      $.ajax({
        url: '/index.php',
        method: 'POST',
        cache: false,

        dataType: 'json',
        data: {action: 'delete_product', product_id_to_delete: product_id},
        success: function (respond, status, jqXHR) {
          if (typeof respond.error === 'undefined') {
          } else {
          }
        },
        error: function (jqXHR, status, errorThrown) {
          console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
        }
      })
  }
}

$(document).ready(function (){
  let url = new URL(window.location.href)

  $('.custom-form__select').on('change', function (event){
    if(event.target.id === 'sortBy'){
      if ($('#sortBy').val() === 'name'){
        url.searchParams.set('sortBy','name')
      } else if ($('#sortBy').val() === 'price'){
        url.searchParams.set('sortBy','price')
      }
      filter_data()
    }
    if(event.target.id === 'sortOrder'){
      if ($('#sortOrder').val() === 'ASC'){
        url.searchParams.set('sortOrder','ASC')
      } else if ($('#sortOrder').val() === 'DESC'){
        url.searchParams.set('sortOrder','DESC')
      }
      filter_data()
    }
  })

  $('.custom-form__checkbox').on('change', function (event){
    if (event.target.parentElement.className === 'custom-form__group'){
      if ($('#new').prop('checked') === true){
        url.searchParams.set('new','on')
      }else {
        url.searchParams.delete('new')
      }

      if ($('#sale').prop('checked') === true){
        url.searchParams.set('sale','on')
      }else {
        url.searchParams.delete('sale')
      }
      url.searchParams.set('page','1')
      filter_data()
    }
  })

  $(document).on('click', $('.paginator__item'), function (e){
    if (e.target.className === 'paginator__item'){
      if(e.target.text === '>'){

        if (url.searchParams.get('page') === null){

          url.searchParams.set('page', '2')
          filter_data()

        }else {
          url.searchParams.set('page', String(Number(url.searchParams.get('page')) + 1))
          filter_data()
        }
      }else if (e.target.text === '<'){
        url.searchParams.set('page', String(Number(url.searchParams.get('page')) - 1))
        filter_data()

      }else {
        url.searchParams.set('page', e.target.text)
        filter_data()
      }

    }
  })

  $('.range__line').slider({
    min: $(".range__line").data('min'),
    max: $(".range__line").data('max'),
    values: [$('#minimumPrice').val(), $('#maximumPrice').val()],
    step: 100,
    range: true,
    stop: function (event, ui) {
      $('#minimumPrice').val($('.range__line').slider('values', 0));
      $('#maximumPrice').val($('.range__line').slider('values', 1));
      url.searchParams.set('min', $('.range__line').slider('values', 0))
      url.searchParams.set('max', $('.range__line').slider('values', 1))
      console.log(url)
      url.searchParams.set('page','1')
      filter_data()
    },
    slide: function(event, ui) {
      $('.min-price').text($('.range__line').slider('values', 0) + ' руб.');
      $('.max-price').text($('.range__line').slider('values', 1) + ' руб.');
    }
  })

  function filter_data(){

    let action = 'filter_data'
    let category = ''
    let requestFrom = ''
    if (!(url.pathname === '/route/admin/products/')) {
      requestFrom = 'shop'
      category = url.pathname.split('/')[3]
    } else {
      requestFrom = 'admin'
    }

    let sortBy = $('#sortBy').val()
    let sortOrder = $('#sortOrder').val()
    let min = $('#minimumPrice').val()
    let max = $('#maximumPrice').val()
    let isNew = $('#new').prop('checked') === true ? 'on':0
    let isSale = $('#sale').prop('checked') === true ? 'on':0

    let page = url.searchParams.get('page')


    if (url.searchParams.get('page') === null) {
      url.searchParams.set('page', '1')
      page = 1
    }


    history.pushState(null, null, url.href)

    $.ajax({
      url : '/index.php',
      method : 'POST',
      cache : false,

      dataType : 'json',
      data : {
        requestFrom:requestFrom,
        page:page,
        category:category,
        action:action,
        min:min,
        max:max,
        sortBy:sortBy,
        sortOrder:sortOrder,
        new:isNew,
        sale:isSale
      },
      success : function (respond, status, jqXHR) {
        if(typeof respond.error === 'undefined' ) {
          if (requestFrom === 'shop') {
            $("#productsCount").html(respond.result['productsCount'])
            $(".shop__list").html(respond.result['output']);

          } else if (requestFrom === 'admin') {

            $(".page-products__list").html(respond.result['output']);
          }

          $(".paginator").html(respond.result['pagination'])

        } else {

          $(".shop__list").html('товары не найдены')
        }

      },
      error: function(jqXHR, status, errorThrown) {
        console.log('ОШИБКА AJAX запроса: ' + status, jqXHR);
      }
    })
  }
})

