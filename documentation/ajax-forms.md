# Submitting a form using AJAX

To submit a form using AJAX, use the form's `<input name="action" />` input value as the target URL, and pass the serialized form data as the payload.

**NOTE:** This solution currently will not work with [multi-page](multi-page-forms.md) forms.


## Return values <a href="#return-values" id="return-values" class="docs-anchor">#</a>

The AJAX request must be a `post` request and it will return a JSON object with the following values:

### On successful single-page form post <a href="#return-success" id="return-success" class="docs-anchor">#</a>

* `success` - A boolean value of `true`
* `finished` - A boolean value of `true`
* `returnUrl` - The return URL specified for the form
* `submissionId` - An `int` value of the submission ID if one was generated

### On form error <a href="#return-error" id="return-error" class="docs-anchor">#</a>

* `success` - A boolean value of `false`
* `finished` - A boolean value of `false`
* `errors` - An object of field handles as keys and each containing an array of error messages.
	* An example, if the form's `firstName` and `lastName` fields were required, but not filled out, the returning object would be:

```"errors": {
	"firstName": ["This field is required"],
	"lastName": ["This field is required"]
}```


## Usage in Templates <a href="#templates" id="templates" class="docs-anchor">#</a>

Here's a fully working Bootstrap form AJAX example:

	<script>
	$(function () {
		$("form").on({
			submit: function () {
				var $self = $(this);
				$(".alert.alert-success").remove();
				$("button[type=submit]", $self).prop("disabled", true);

				$.ajax({
					url: $("input[name=action]", $(this)).val(),
					type: "post",
					dataType: "json",
					data: $(this).serialize(),
					success: function (response) {
						$("ul.errors.help-block", $self).remove();
						$(".has-error", $self).removeClass("has-error");

						if (response.success && response.finished) {
							$self[0].reset();

							var $successMessage = $("<div>", {"class": "alert alert-success"})
								.append("<p>Form submitted successfully</p>", {"class": "lead"});

							$self.before($successMessage);

						} else if (response.errors) {
							for (var key in response.errors) {
								if (!response.errors.hasOwnProperty(key)) continue;
								var messages = response.errors[key];
								var $errors  = $("<ul>", {"class": "errors help-block"});
								for (var i = 0; i < messages.length; i++) {
									var $li = $("<li>");
									$li.html(messages[i]);
									$errors.append($li);
								}

								var $input = $("*[name=" + key + "], *[name='" + key + "[]']");

								const $formGroup = $input.parents(".form-group");
								$formGroup.addClass("has-error");

								$formGroup.append($errors);
							}
						}

						$("button[type=submit]", $self).prop("disabled", false);
					}
				});

				return false;
			}
		});
	});
	</script>
