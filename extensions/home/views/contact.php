<div class="splitform" style="float: left;">
	<h2>Contact Us</h2>
	<p>
	Hi! It's always nice to hear from our visitors with feedback on how to improve our services. If you had a problem we are sorry and hope we can help. Either way send us a message with the form below and we will try our best to reply in the next 24 hours.
	</p>
	<form action="contact_form_submitted" method="POST" name = "contact_form">
	
		<label>Name: <input type="text" name="fullname" placeholder="Full Name" /></label>
		<br />
		<label>Email: <input type="text" name="emailaddress" placeholder="Email address"></label>
		<br />
		<label>Subject: <input type="text" name="subject" placeholder="Subject"></label>
		<br />
		<label>Message: <textarea name="message" placeholder="Your message here."></textarea></label>
		<br />
		Reason:
		<span class="radiolist">
			<label> <input type="radio" name="reason" value="question"> Question</label>
			<br />
			<label> <input type="radio" name="reason" value="complaint"> Complaint</label>
			<br />
			<label> <input type="radio" name="reason" value="request"> Request</label>
			<br />
			<label> <input type="radio" name="reason" value="bug"> Broken feature</label>
			<br />
			<label> <input type="radio" name="reason" value="adult"> Inappropriate Content</label>
		</span>
		<br />
		<br />
		<input type="submit" name="submit" value="Send Message &raquo;">
		<br />
	</form>
</div>

<script>
	var contactValidator  = new Validator("contact_form");
	contactValidator.addValidation("fullname","req","Please provide your name!");
	contactValidator.addValidation("emailaddress","req","Please provide your email!");
	contactValidator.addValidation("message","req","Please provide a message so we know how to help you!");
	contactValidator.addValidation("reason","req","Please enter a valid reason!");
</script><!--End of validator script used to ensure all required fields are provided -->
<!-- Created following a tutorial: http://www.html-form-guide.com/email-form/html-email-form.html-->

<div class="splitform" style="float: right;">
	<h2>Report Abuse</h2>
	<p>
	We understand the importance of courtesy on the web. If somebody has violated our <a href="/home/terms">Terms of Service</a> we'd like to hear about it so we can make our services more enjoyable for all of our guests.
	</p>
<br />
	<form action="report_submitted" method="POST" name = "report_form">
		<label>Name: <input type="text" name="fullname" placeholder="Full Name" /></label>
		<br />
		<label>Email: <input type="text" name="emailaddress" placeholder="Automatically filled email..."></label>
		<br />
		<label>Offender: <input type="text" name="subject" placeholder="Offending user's username"></label>
		<br />
		<label>Explanation: <textarea name="message" placeholder="An explanation of the offensive behaviour."></textarea></label>
		<br />
		Reason:
		<span class="radiolist">
			<label> <input type="radio" name="reason" value="profane"> Profane/inappropriate content.</label>
			<br />
			<label> <input type="radio" name="reason" value="harass"> Harassment</label>
			<br />
			<label> <input type="radio" name="reason" value="fraud"> Fraudulent sales</label>
			<br />
			<label> <input type="radio" name="reason" value="scam"> Scamming</label>
			<br />
			<label> <input type="radio" name="reason" value="other"> Other</label>
		</span>
		<br />
		<br />
		<input type="submit" name="submit" value="Send Message &raquo;">
		<br />
	</form>
</div>
<script>
	var reportValidator  = new Validator("report_form");
	reportValidator.addValidation("fullname","req","Please provide your name!");
	reportValidator.addValidation("emailaddress","req","Please provide your email!");
	reportValidator.addValidation("message","req","Please provide a message so we know how to help you!");
	reportValidator.addValidation("reason","req","Please enter a valid reason!");
</script><!--End of validator script used to ensure all required fields are provided -->