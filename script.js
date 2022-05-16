function validate() {
  try {
    var pw = document.getElementById("pw").value;
    var email = document.getElementById("email").value;

    if (pw == false && email == false) {
      alert("Both fields must be filled out.");
      return false;
    }

    if (pw == false) {
      alert("Password must be filled out.");
      console.log("password blank");
      return false;
    }

    if (email == false) {
      alert("Email must be filled out.");
      return false;
    }

    if (!email.includes("@")) {
      alert("Invalid email.");
      return false;
    }
    return true;
  } catch (e) {
    return false;
  }
}

var n = $("#position_fields").children().length;
var m = $("#course_fields").children().length;

$("document").ready(() => {
  $("#addPos").click(function (event) {
    event.preventDefault();

    if (n > 9) {
      alert("No more than 9 notes may be added");
      return;
    }

    n++;

    $("#position_fields").append(
      `<div id="position${n}">
                    <p>Year: <input type="text" name="year${n}" value="" />
                    <input type="button" value="-" onclick="$('#position${n}').remove();return false" /></p>
                    <textarea name="desc${n}" rows="6" cols="70"></textarea>
                    <p></p>
                    </div>`
    );
  });
  $("#addCourse").click(function (event) {
    event.preventDefault();

    if (m > 9) {
      alert("No more than 9 trainings may be added");
      return;
    }

    m++;

    $("#course_fields").append(
      `<div id="course${m}">
        <p>Year: <input type ="text" name="course_year${m}" value="" />
        <input type="button" value="-" onclick="$('#course${m}').remove();return false" /></p>
        <input type="text" size="70" name="course${m}" class="course" />
    </div><p></p`
    );

    $(".course").autocomplete({
      source: "course.php",
    });
  });

  $(".course").autocomplete({
    source: "course.php",
  });
});
