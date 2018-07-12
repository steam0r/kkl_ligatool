var SetFixture = function () {
  this.$setFixture = jQuery('.kkl-match-fixture');
  this.$locationForm = jQuery('#location');
  this.$datePicker = jQuery('#fixture');

  this.$waitMsg = jQuery('.kkl-match-fixture .wait');
  this.$successMsg = jQuery('.kkl-match-fixture .success');
};

/**
 * Init SetFixture
 */
SetFixture.prototype.init = function () {
  console.log("initing");
  this.eventHandler();
  this.initDatepicker();
};


/**
 * Event Handler
 *
 * Change Match and choose Location
 * Submit Form
 */
SetFixture.prototype.eventHandler = function () {
  var _this = this;

  jQuery(document).on('change', '#match', function () {
    var locationId = parseInt(jQuery(this + ':selected').attr('data-location'));

    if (locationId) {
      _this.$locationForm.val(locationId);
    } else {
      _this.$locationForm.val('unknown');
    }
  });

  jQuery(document).on('submit', '#set_match_fixture', function (event) {
    event.preventDefault();

    _this.validateForm('set_match_fixture', function (formData) {
      _this.$waitMsg.addClass('-in');
      var sendData = {
        email: formData.email,
        match: {
          fixture: formData.fixture
        }
      };

      if (formData.location !== 'unknown') {
        sendData.match['location'] = formData.location
      }

      $.ajax({
        url: formData.apiUrl + '/wp-json/kkl/v1/matches/' + formData.match + '/fixture',
        type: 'PATCH',
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(sendData),
        success: function (response) {
          console.log('submit success', response);
          _this.$waitMsg.removeClass('-in');
          _this.$successMsg.addClass('-in');
        },
        error: function (e) {
          console.error('submit error', e);
        }
      });
    });

  });
};


/**
 * Init DateTimePicker
 * https://github.com/xdan/datetimepicker
 */
SetFixture.prototype.initDatepicker = function () {
  this.$datePicker.datetimepicker({
    i18n: {
      de: {
        months: [
          'Januar', 'Februar', 'März', 'April',
          'Mai', 'Juni', 'Juli', 'August',
          'September', 'Oktober', 'November', 'Dezember'
        ],
        dayOfWeek: [
          "So.", "Mo", "Di", "Mi",
          "Do", "Fr", "Sa."
        ]
      }
    },
    format: 'Y-m-d H:m',
    defaultTime: "20:00"
  });
};


/**
 * Validate and return Form Data
 *
 * @param form_id
 * @param callback
 */
SetFixture.prototype.validateForm = function (form_id, callback) {
  var $form = jQuery('#' + form_id);
  var i = 0;
  data = {
    formular: form_id,
    apiUrl: $form.attr('data-url')
  };

  // revert form errors
  $form.find('.required').removeClass('has-error');

  // find & validate formfields
  $form.find('input, select').each(function (index) {
    if (jQuery(this).attr('type') === 'text' || jQuery(this).attr('type') === 'number') {
      if (jQuery(this).val() === '') {
        i++;
        jQuery(this).addClass('has-error');
      } else {
        data[jQuery(this).attr('name')] = jQuery(this).val();
      }

    } else if (jQuery(this).attr('type') === 'email') {
      var email = jQuery.trim(jQuery(this).val());
      var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,12})$/;

      if (!regex.test(email)) {
        i++;
        jQuery(this).addClass('has-error');
      } else {
        data[jQuery(this).attr('name')] = jQuery(this).val();
      }
    } else {
      data[jQuery(this).attr('name')] = jQuery(this).val();
    }
  });

  if (i === 0) {
    callback(data);
  }
};


/**
 *
 * @type {SetFixture}
 */
jQuery(document).ready(function () {
  console.log("INCLUDED");
  var setFixture = new SetFixture();
  setFixture.init();
});
