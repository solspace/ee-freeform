const datepickers = document.querySelectorAll('.form-datepicker');

for (let i = 0; i < datepickers.length; i++) {
  const picker = datepickers[i],
      dateFormat = picker.dataset.datepickerFormat,
      enableTime = picker.dataset.datepickerEnabletime,
      enableDate = picker.dataset.datepickerEnabledate,
      clock_24h = picker.dataset.datepickerClock_24h;

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
