"use strict";$(function(){$(".btn.add-template").on({click:function(e){var t=$(e.target);return $.ajax({url:t.attr("href"),type:"post",dataType:"json",data:{force_file:!0,name:"sample_template",templateName:"sample_template"},success:function(e){if(e.errors&&e.errors.length)for(var t=0;t<e.errors.length;t++)alert(e.errors[t]);else window.location.reload(!1)}}),e.preventDefault(),e.stopPropagation(),!1}})});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNldHRpbmdzLmpzIl0sIm5hbWVzIjpbIiQiLCJvbiIsImNsaWNrIiwiZSIsInNlbGYiLCJ0YXJnZXQiLCJhamF4IiwidXJsIiwiYXR0ciIsInR5cGUiLCJkYXRhVHlwZSIsImRhdGEiLCJmb3JjZV9maWxlIiwibmFtZSIsInRlbXBsYXRlTmFtZSIsInN1Y2Nlc3MiLCJyZXNwb25zZSIsImVycm9ycyIsImxlbmd0aCIsImkiLCJhbGVydCIsIndpbmRvdyIsImxvY2F0aW9uIiwicmVsb2FkIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iXSwibWFwcGluZ3MiOiJBQUFBLFlBQUFBLEdBQUUsV0FDQUEsRUFBRSxxQkFBcUJDLElBQ3JCQyxNQUFPLFNBQUNDLEdBQ04sR0FBTUMsR0FBT0osRUFBRUcsRUFBRUUsT0F3QmpCLE9BdEJBTCxHQUFFTSxNQUNBQyxJQUFLSCxFQUFLSSxLQUFLLFFBQ2ZDLEtBQU0sT0FDTkMsU0FBVSxPQUNWQyxNQUNFQyxZQUFZLEVBQ1pDLEtBQU0sa0JBQ05DLGFBQWMsbUJBRWhCQyxRQUFTLFNBQUNDLEdBQ1IsR0FBSUEsRUFBU0MsUUFBVUQsRUFBU0MsT0FBT0MsT0FDckMsSUFBSyxHQUFJQyxHQUFJLEVBQUdBLEVBQUlILEVBQVNDLE9BQU9DLE9BQVFDLElBQzFDQyxNQUFNSixFQUFTQyxPQUFPRSxRQUd4QkUsUUFBT0MsU0FBU0MsUUFBTyxNQUs3QnBCLEVBQUVxQixpQkFDRnJCLEVBQUVzQixtQkFDSyIsImZpbGUiOiJzZXR0aW5ncy5qcyIsInNvdXJjZXNDb250ZW50IjpbIiQoKCkgPT4ge1xuICAkKCcuYnRuLmFkZC10ZW1wbGF0ZScpLm9uKHtcbiAgICBjbGljazogKGUpID0+IHtcbiAgICAgIGNvbnN0IHNlbGYgPSAkKGUudGFyZ2V0KTtcblxuICAgICAgJC5hamF4KHtcbiAgICAgICAgdXJsOiBzZWxmLmF0dHIoJ2hyZWYnKSxcbiAgICAgICAgdHlwZTogJ3Bvc3QnLFxuICAgICAgICBkYXRhVHlwZTogJ2pzb24nLFxuICAgICAgICBkYXRhOiB7XG4gICAgICAgICAgZm9yY2VfZmlsZTogdHJ1ZSxcbiAgICAgICAgICBuYW1lOiAnc2FtcGxlX3RlbXBsYXRlJyxcbiAgICAgICAgICB0ZW1wbGF0ZU5hbWU6ICdzYW1wbGVfdGVtcGxhdGUnLFxuICAgICAgICB9LFxuICAgICAgICBzdWNjZXNzOiAocmVzcG9uc2UpID0+IHtcbiAgICAgICAgICBpZiAocmVzcG9uc2UuZXJyb3JzICYmIHJlc3BvbnNlLmVycm9ycy5sZW5ndGgpIHtcbiAgICAgICAgICAgIGZvciAobGV0IGkgPSAwOyBpIDwgcmVzcG9uc2UuZXJyb3JzLmxlbmd0aDsgaSsrKSB7XG4gICAgICAgICAgICAgIGFsZXJ0KHJlc3BvbnNlLmVycm9yc1tpXSk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIHdpbmRvdy5sb2NhdGlvbi5yZWxvYWQoZmFsc2UpO1xuICAgICAgICAgIH1cbiAgICAgICAgfVxuICAgICAgfSk7XG5cbiAgICAgIGUucHJldmVudERlZmF1bHQoKTtcbiAgICAgIGUuc3RvcFByb3BhZ2F0aW9uKCk7XG4gICAgICByZXR1cm4gZmFsc2U7XG4gICAgfVxuICB9KVxufSk7XG4iXX0=
