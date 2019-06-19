let SetFixture = function () {
    this.$fixtureForm = jQuery('#set_match_fixture');

    this.$locationForm = this.$fixtureForm.find('select#location');
    this.$datePicker = this.$fixtureForm.find('input#fixture');

    this.$notification = this.$fixtureForm.find('[data-notification]');
};

/**
 * Init SetFixture
 */
SetFixture.prototype.init = function () {
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
    let _this = this;

    jQuery(document).on('change', '#match', function () {
        let locationId = parseInt(jQuery(this + ':selected').attr('data-location'));

        if (locationId) {
            _this.$locationForm.val(locationId);
        } else {
            _this.$locationForm.val('unknown');
        }
    });

    jQuery(document).on('submit', '#set_match_fixture', function (event) {
        event.preventDefault();

        _this.validateForm('set_match_fixture', function (formData) {
            let sendData = {
                email: formData.email,
                match: {
                    fixture: formData.fixture
                }
            };

            if (formData.location !== 'unknown') {
                sendData.match['location'] = formData.location
            }

            _this.disableSubmitButton(true);

            _this.sendData(formData, sendData);
        });

    });
};


/**
 *
 * @param formData
 * @param sendData
 */
SetFixture.prototype.sendData = function (formData, sendData) {
    let _this = this;

    jQuery.ajax({
        url: formData.apiUrl + '/wp-json/kkl/v1/matches/' + formData.match + '/fixture',
        type: 'PATCH',
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(sendData),
        success: function (response) {
            _this.notification('Termin erfolgreich gespeichert!');
            _this.disableSubmitButton(false);
        },
        error: function (e) {
            if (e.status === 401) {
                _this.notification('Sorry, aber du darfst das nicht ...');
                _this.disableSubmitButton(false);
            }
        }
    });
};


/**
 *
 * @param message
 */
SetFixture.prototype.notification = function (message) {
    let _this = this;
    this.$notification.text(message).addClass('-in');

    setTimeout(function () {
        _this.$notification.removeClass('-in');
    }, 3000);
};


/**
 *
 * @param state
 */
SetFixture.prototype.disableSubmitButton = function (state) {
    this.$fixtureForm.find('input').prop('disabled', state);
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
    let $form = jQuery('#' + form_id);
    let i = 0;
    let data = {
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
            let email = jQuery.trim(jQuery(this).val());
            let regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,12})$/;

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
    let setFixture = new SetFixture();
    setFixture.init();
});
