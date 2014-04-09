var casper = require("casper").create({
    verbose: true, logLevel: "debug"
});

casper.start('https://ceac.state.gov/CEACStatTracker/Status.aspx?App=NIV', function(response) {
    this.echo(this.getTitle());
    //require('utils').dump(response);
    //document.getElementById("ctl00_ContentPlaceHolder1_txbCase").value="123";
    //document.getElementById("ctl00_ContentPlaceHolder1_btnSubmit").click();
    //this.echo(document.getElementById("ctl00_ContentPlaceHolder1_ucApplicationStatusView_lblStatus").html);
});

casper.thenEvaluate(function(){
	
	this.sendKeys('input#ctl00_ContentPlaceHolder1_txbCase', "AA003DOTHO");
	//this.click('input[id="ctl00_ContentPlaceHolder1_btnSubmit"]');
	//this.echo(this.getHTML());
	//this.debugPage();
});
casper.thenEvaluate(function(){
	
	this.sendKeys('input#ctl00_ContentPlaceHolder1_txbCase', "AA003DOTHO");
	//this.click('input[id="ctl00_ContentPlaceHolder1_btnSubmit"]');
	//this.echo(this.getHTML());
	//this.debugPage();
});


casper.run();
