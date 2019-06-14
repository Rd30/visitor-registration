$(document).ready(function(){

  var visitorInfo = {
    name:null,
    company:null,
    type:null,
    phone:null,
    host:null
  };

  // Retreive all the hosts(employees) list from index.php and push it into an array.
  var hostsList = [];
  for(var i in getAllHosts){
    hostsList.push(getAllHosts[i]['User'].toLowerCase());
  }

  // Reveal the visitor registration form.
  $('#startBtn').click(function(){
      $('#landing').slideUp(2000);
      $('#pri-form').slideToggle(2000);
  });

  // Search by department 'div' hidden on load.
  $('#deptNameDiv').attr("hidden", true);
  $('#deptLabelModal').attr("hidden", true);
  
  // jQuery custom validation for US and international phone numbers.
  jQuery.validator.addMethod('intlphone', function(value){
    return (value.match(/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}(\s*(ext|x)\s*\.?:?\s*([0-9]+))?$/));
  }, 'Please enter a valid phone number');

  // jQuery custom validation for Visitor Type select item.
  $.validator.addMethod("valueNotEquals", function(value, element, arg){
    return arg !== value;
  }, "Value must not equal arg.");

  // jQuery custom validation - allow only alphabets and spaces in the input.
  $.validator.addMethod("alphabetsSpaces", function(value, element) {
	   return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
  }, "Letters and spaces only please");

  // jQuery custom validation - force visitors to select host from the list.
  $.validator.addMethod("selectFromList", function(value, element) {
     var inputHostName = $("#inputHostSearch").val().toLowerCase();
     return jQuery.inArray(inputHostName, hostsList) != -1
   }, "Select host from the suggestion list");

  // jQuery validators
  $('#visitreg-form').validate({
    rules: {
      inputName:{
        required: true,
        minlength: 2,
        alphabetsSpaces: true
      },
      inputCompany:{
        required: true,
        minlength: 2,
        alphabetsSpaces: true
      },
      inputHostSearch:{
        required: true,
        selectFromList: true,
      },
      inputDeptSearch:{
        required: true,
      },
      inputPhone:{
        required: true,
        intlphone: true
      },
      selectVisitorType:{
        valueNotEquals: ""
      }
    },
    messages: {
      inputName: "Error: Please enter your valid full name in the format [FirstName] [LastName]. No special characters are allowed.",
      inputPhone: "Error: Please enter a valid cellphone number.",
      inputCompany: "Error: Please enter the company you are representing. No special characters are allowed.",
      inputHostSearch: "Error: Please select a valid host name from the suggestion list.",
      inputDeptSearch: "Error: Please select a valid department from the suggestion list.",
      selectVisitorType: "Error: Please select a valid visitor type.",
    },
    errorElement : 'div',
    errorLabelContainer: '.msgBox',
    errorClass: "exclamation alert alert-danger",
    highlight:function(element, errorClass, validClass) {
			$(element).parents('.form-group').addClass('has-error');
		},
		unhighlight: function(element, errorClass, validClass) {
			$(element).parents('.controls').removeClass('has-error');
			$(element).parents('.form-group').addClass('has-success');
		}
  });

  //Check here to reveal Search by department and hide host name 'div'.
  $('#checkNoHost').on('click', function () {
       if ($(this).prop('checked')) {
         $('#hostNameDiv').attr("hidden", true);
         $('#deptNameDiv').attr("hidden", false);
         $('#checkRemember').slideUp(2000);
         $('#hostLabelModal').attr("hidden", true);
         $('#deptLabelModal').attr("hidden", false);
       } else {
         $('#deptNameDiv').attr("hidden", true);
         $('#deptLabelModal').attr("hidden", true);
       }
   });

  // Search the Host Name (employee)
  $('#inputHostSearch').on("input", function(e){
    var inputVal = $(this).val();
    var resultDropdown = $(this).siblings(".result");
    if(inputVal.length){
      $.get("hostSearch.php", {term: inputVal}).done(function(data){
          resultDropdown.html(data); // Display the returned HostName data in browser.
      });
    }else{
      resultDropdown.empty();
    }
  });

  // Keyboard navigation for hostname
  $(document).on("keydown", "#inputHostSearch", function(e){
    if(e.which === 40){    // keyboard down arrow
      var lenCount = $("#hostNameDiv > div > div > p.active").length;
      if(lenCount == 0){
        $("#hostNameDiv > div > div > p:nth-child(1)").addClass("active");
      }else{
        var storeTarget = $("#hostNameDiv > div > div > p.active").next();
        $("#hostNameDiv > div > div > p.active").removeClass("active");
        storeTarget.addClass("active");
      }
    }
    if(e.which === 38){    // keyboard up arrow key
      var lenCount = $("#hostNameDiv > div > div > p.active").length;
      if(lenCount == 0){
         $("#hostNameDiv > div > div > p:nth-child(1)").addClass("active");
      }else{
        let storeTarget	= $("#hostNameDiv > div > div > p.active").prev();
        $("#hostNameDiv > div > div > p.active").removeClass("active");
        storeTarget.addClass("active");
      }
    }
    if(e.which === 13){    //keyboard enter key
      let hostValue = $("#hostNameDiv > div > div > p.active").text();
      $("#inputHostSearch").val(hostValue);
      $("#hostNameDiv > div > div").empty();
    }
  });
  
  $(".result").on("mousedown", "p", function(e){    
    $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
    $(this).parents(".result").empty();
  });

  // Search the DepartmentName
  $('#inputDeptSearch').on("input", function(){
    var inputDeptVal = $(this).val();
    var deptResDropdown = $(this).siblings(".deptResult");
    if(inputDeptVal.length){
        $.get("deptSearch.php", {term: inputDeptVal}).done(function(data){
            deptResDropdown.html(data); // Display the returned DepartmentName data in browser.
        });
    } else{
        deptResDropdown.empty();
    }
  });

  // Keyboard navigation for deptname
  $(document).on("keydown", "#inputDeptSearch", function(e){
    if(e.which === 40){    // keyboard down arrow
      //var deptLenCount = $("#deptNameDiv > div > div > p.active").length;
      if($("#deptNameDiv > div > div > p.active").length == 0){
        $("#deptNameDiv > div > div > p:nth-child(1)").addClass("active");
      }else{
        var storeTarget = $("#deptNameDiv > div > div > p.active").next();
        $("#deptNameDiv > div > div > p.active").removeClass("active");
        storeTarget.addClass("active");
      }
    }
    if(e.which === 38){    // keyboard up arrow key
      //var deptLenCount = $("#deptNameDiv > div > div > p.active").length;
      if($("#deptNameDiv > div > div > p.active").length == 0){
         $("#deptNameDiv > div > div > p:nth-child(1)").addClass("active");
      }else{
        var storeTarget	= $("#deptNameDiv > div > div > p.active").prev();
        $("#deptNameDiv > div > div > p.active").removeClass("active");
        storeTarget.addClass("active");
      }
    }
    if(e.which === 13){    //keyboard enter key
      var deptValue = $("#deptNameDiv > div > div > p.active").text();
      $("#inputDeptSearch").val(deptValue);
      $("#deptNameDiv > div > div").empty();
    }
  });

  // Set DepartmentName input value on click of the result item.
  $(document).on("click", ".deptResult p", function(){
    $(this).parents(".dept-search-box").find('input[type="text"]').val($(this).text());
    $(this).parents(".deptResult").empty();
  });

  //Populate the visitor info into the modal confirmation dialog
  $('#register-btn').on('click', function(e){
    if($('#deptNameDiv').is(':hidden')){
      var inputHostName = $("#inputHostSearch").val().toLowerCase();
      if(($.inArray(inputHostName, hostsList) != -1)&&($('#visitreg-form').valid())){
        $('#nameVal').val($('#inputName').val());
        $('#companyVal').val($('#inputCompany').val());
        $('#hostNameVal').val($('#inputHostSearch').val());
        $('#phoneVal').val($('#inputPhone').val());
        $('#visitorTypeVal').val($('#sel-visitor-type option:selected').val());
      }else{
        $('#alert-modal').modal("show");
        $("#error-msg").html("<strong>Please correct the errors before you proceed.</strong>");
        return false;
      }
    }else{
      $('#nameVal').val($('#inputName').val());
      $('#companyVal').val($('#inputCompany').val());
      $('#hostNameVal').val($('#inputDeptSearch').val());
      $('#phoneVal').val($('#inputPhone').val());
      $('#visitorTypeVal').val($('#sel-visitor-type option:selected').val());
    }
  });

  var spinner = $('#loader'); //loading spinner id in index.
  
   //Confirm registration on submit.
  $('#confirm-reg-form').on('submit', function(e){
    e.preventDefault();
    spinner.show();
    $.ajax({
      type: 'post',
      url: 'visitorRegistration.php',
      data: $('#confirm-reg-form').serialize(),
      dataType:'json',
      success: function(response){
	     emailToHost(); 
        window.location = "http://nd-force.entegris.com/visitorRegistration/index.php?success="+response.success+"&host="+response.host+"&phone="+response.phone+"&name="+response.name+"&id="+response.id+"&type="+response.type+"&startTime="+response.startTime+"&company="+response.company;
      }
    });
    setTimeout(function() {
       spinner.hide();
     }, 30000);
  });
	
	//Send an email to host to let know of the visitor.
   function emailToHost(){    
    $.ajax({
     url:'handleEmailHost.php',
     type:'post',
     data:{
      sendEmail:'sendEmail'
     },
     success:function(response) {
       alert("Ajax Success : Email sent to the host.");
     }
    });
  }  

});
