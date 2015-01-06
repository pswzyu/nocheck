var args = require("system").args;

if (args.length == 2)
{
    case_id = args[1];
}else
{
    console.log("failed,number of argument is not correct!");
    phantom.exit();
}
var page = require('webpage').create();
page.settings.userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.71 Safari/537.36';
//
page.open("https://ceac.state.gov/CEACStatTracker/Status.aspx?App=NIV", function (status){
    //page.render('new.png');
    //console.log(status);
    var data = page.evaluate("function () {\
        document.querySelector('input#ctl00_ContentPlaceHolder1_txbCase').setAttribute('value', '"+case_id+"');\
        document.querySelector('input#ctl00_ContentPlaceHolder1_btnSubmit').click();}");
    var check_times = 0;
    var process_id = setInterval(check_ajax, 1500);
    function check_ajax()
    {
        ++ check_times;

        // check the format of the ds160 id
        var validation_result = page.evaluate(function(){
            return document.querySelector("div#ctl00_ContentPlaceHolder1_ValidationSummary1 ul");
        });
        if (validation_result.attributes != null)
        {
            console.log("failed,Invalid caseid!");
            phantom.exit();
        }
        // check the result
        var result_div = page.evaluate(function(){
            return document.querySelector("span#ctl00_ContentPlaceHolder1_ucApplicationStatusView_lblStatus");
        });
        if (result_div.attributes != null)
        {
            var visa_status = result_div.innerHTML;
            var visa_detail = page.evaluate(function(){
                //return document.querySelector("div.status-contents");
                var status = document.querySelector("span#ctl00_ContentPlaceHolder1_ucApplicationStatusView_lblStatus").innerHTML;
                var create_time = document.querySelector("span#ctl00_ContentPlaceHolder1_ucApplicationStatusView_lblSubmitDate").innerHTML;
                var update_time = document.querySelector("span#ctl00_ContentPlaceHolder1_ucApplicationStatusView_lblStatusDate").innerHTML;
                return status+","+create_time+","+update_time;
            });
            //visa_detail = visa_detail.innerHTML;
            console.log("success,"+visa_detail);
            phantom.exit();
        }
        if (check_times >= 20)
        {
            console.log("failed,Time limit exceeded!");
            phantom.exit();
        }
    }
	
});



