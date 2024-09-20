jQuery(document).ready(function($) {
  Tablesaw.init(); 

  $('[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
      var target = $(e.target).data("bs-target");

      var $table = $(target).find('table.tablesaw');

      tablesawRefresh($table)
  });
  const url = new URLSearchParams(location.search)

  if (url.get('tab')) {
    const hash = '#' + url.get('tab') 

    showTab(hash)
  }

  $(document).on('click', '[data-toggle=collapse]', function() {
    const target = $(this).data('target')
    $(target).collapse('toggle')
  })

  $(document).on('submit', '.form-filter', function(e) {
    e.preventDefault()

    const url = new URLSearchParams(location.search)
    const form = $(this).serialize()
    const search = form.split('&')
    search.map(s => {
      const s1 = s.split('=')
      if (s1[1])
        url.set(s1[0], s1[1])
    })
    location.href = location.pathname + '?' + url.toString()
  })

  $(document).on('click', '[data-bs-target]', function(e) {
    const id = $(e.target).data('bs-target')

    
    const url = new URLSearchParams(location.search)
    
    url.set('tab', id.substr(1))
    
    history.pushState('','', '?'+url.toString())

    if (location.pathname.match(/user\/\d+\/page/i) && location.pathname.match(/user\/\d+\/page/i).length) {
      const exc = location.pathname.match(/page\/\d+\//i)
      location.href = location.pathname.replace(exc[0], '') + location.search
    }
    showTab(id)
  })

  $(document).on('change', '#input_auto', function(e) {
    const form = $(this).closest('form')

    if (e.target.checked) {
      form.find('.investors_profit').hide()
      form.find('.investors_refund').hide()
    } else  if (!e.target.checked && $('input[name="transaction_type"]:checked').val() == 4){
      form.find('.investors_profit').show()
      form.find('.investors_refund').hide()
    } else  if (!e.target.checked && $('input[name="transaction_type"]:checked').val() == 3){
      form.find('.investors_refund').show()
      form.find('.investors_profit').hide()
    }
    form.find('.form__amount').toggle()

    form.find('input.form-control').val(0)
  })

  $(document).on('change', '.project_profit_prepare input[name="transaction_type"]', function(e) {
    const type = $('input[name="transaction_type"]:checked').val()
    const form = $(this).closest('form')

    if (type == 3) {
      $('.investors_profit').hide()
      $('.investors_refund').show()
      
    } else if (type == 4) {
      $('.investors_profit').show()
      $('.investors_refund').hide()

    }
    $('#input_auto').removeAttr('checked')
    $('.form__amount').hide()

    form.find('input.form-control').val(0)
  })

  // $(document).on('input', '#summa', function(e) {
  //   const form = $(this).closest('form')
  //   const auto = form.find('#input_auto').get(0).checked
  //   const value = parseFloat(e.target.value)

  //   if (auto) {
  //     const count = form.find('input[name^="user["').length
  //     form.find('input[name^="user["').val((value / count).toFixed(2))
  //   }
  // })

  $(document).on('submit', 'form.project_profit_prepare', function(e) {
    e.preventDefault()

    const fancy = Fancybox.getInstance()
    const data = $(this).serialize()

    fancy.close()

    projectProfitHandler(data.toString())
  })

  $(document).on('submit', 'form.user-checkout-form', function(e) {
    e.preventDefault()

    const fancy = Fancybox.getInstance()
    const data = $(this).serialize()

    fancy.close()

    checkoutPartiallyHandler(data.toString())
  })

  $(document).on('click', '[data-post-delete]', function(e) {
    e.preventDefault()
  
    const _this = $(this)
    const url = _this.attr('href')
    const backurl = new URL(_this.data('backurl'))
    const search = new URLSearchParams(backurl.search)
    const type = _this.data('post-type')
  
    $.ajax({
      url: url,
      method: 'POST',
      beforeSend: function() {
        _this.attr('disabled', true)
        _this.text('Удаление..')
      },
      complete: function() {
        search.set('delete', 'success')
        search.set('deleted_type', type)
        location.href = backurl.pathname + '?' + search.toString()
      },
    })
  })

  $(document).on('click', '.filter-input__btn', function() {
    const parent = $(this).parent();
    
    if (!parent.hasClass('is-active')) {
        $('.filter-input').removeClass('is-active');
    }
    
    parent.toggleClass('is-active');
  });

  function showTab(tab) {
    $('[data-bs-toggle="tab"]').removeClass('active')
    $('.tab-pane').removeClass('active')
    
    $('[data-bs-target="'+tab+'"]').addClass('active')
    $(tab).toggleClass('active')

    const btn = $(tab).attr('id')
    $(`#${btn}-tab`).trigger('shown.bs.tab')
  }

})
// const tooltip = new bootstrap.Tooltip('#example', {
//   boundary: document.body // or document.querySelector('#boundary')
// })
function clearDate(_this) {
  const url = new URLSearchParams(location.search)
  const form = jQuery(_this).parents('form')
  const inputs = form.find('input[type=date], select.form-date')
  inputs.each((i, el) => {
    if (el !== _this) {
      el.value = ''
      el.removeAttribute('value')
      url.delete(el.name)
    }
  })

  history.pushState(null, '', '?' + url.toString())
}

function clearYearMonth(_this) {
  const url = new URLSearchParams(location.search)
  const form = jQuery(_this).parents('form')

  const selects = form.find('select.form-date')

  selects.each((i, el) => {
    el.value = ''
    el.removeAttribute('value')
    url.delete(el.name)
  })


  history.pushState(null, '', '?' + url.toString())
}

function filterSelect(_this) {
  jQuery(_this).parents('form').trigger('submit')

  const url = new URL(window.location.href);
  const pathname = url.pathname.replace(/\/page\/\d+\//, '/');
  const newUrl = `${pathname}${url.search}`;
  const label = _this.closest('.filter-input').querySelector('label span.values')
  let values = []

  window.history.replaceState(null, '', newUrl);

  setTimeout(() => {
    switch(_this.tagName) {
      case 'SELECT':
        const options = _this.querySelectorAll('option')
        console.log(_this.value);
        
        options.forEach(opt => {
          if (opt.selected)
            values.push(opt.textContent.trim())
        });
        break;
      case 'INPUT':
        if (_this.type == 'date') {
  
        }
  
    }
  
    // console.log(_this.value)
    label.textContent = ''
    label.append(values.join(', '))
  }, 100)
  
}

function clearAllTimeInput(_this) {
  if (!_this.checked) {
    const url = new URLSearchParams(location.search)

    url.delete('all_time')
    history.pushState(null, '', '?' + url.toString())
  }
}

function toggleFilter(btn) {
  const form = jQuery(btn).parents('form')
  const filter = form.find('.hidden-filter')

  filter.toggle(400)
}

function formSortHandler(_this) {
  jQuery(_this).closest('form').trigger('submit');
  
  jQuery('.sort-label').removeClass('active')
  
  jQuery(_this).parent().addClass('active')
}

function tablesawRefresh(table) {
  if (table.data('tablesaw')) {
    table.data('tablesaw').destroy();
  }

  table.tablesaw({
    mode: 'swipe'
  });
}