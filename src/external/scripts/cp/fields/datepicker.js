if (typeof Object.assign != 'function') {
  Object.assign = function(target) {
    'use strict';
    if (target == null) {
      throw new TypeError('Cannot convert undefined or null to object');
    }

    target = Object(target);
    for (var index = 1; index < arguments.length; index++) {
      var source = arguments[index];
      if (source != null) {
        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }
    }
    return target;
  };
}

const datepickers = document.querySelectorAll('.form-datepicker');

for (let i = 0; i < datepickers.length; i++) {
  const picker = datepickers[i];

  const dateFormat = picker.getAttribute("data-datepicker-format"),
        enableTime   = picker.getAttribute("data-datepicker-enabletime"),
        enableDate   = picker.getAttribute("data-datepicker-enabledate"),
        clock_24h    = picker.getAttribute("data-datepicker-clock_24h");

  flatpickr(picker, {
    disableMobile: true,
    allowInput: true,
    dateFormat: dateFormat,
    enableTime: !!enableTime,
    noCalendar: !enableDate,
    time_24hr: !!clock_24h,
    minuteIncrement: 1,
    hourIncrement: 1,
  });
}
