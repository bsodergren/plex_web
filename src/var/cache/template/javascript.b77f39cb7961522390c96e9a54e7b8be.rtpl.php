<?php if(!class_exists('Rain\Tpl')){exit;}?><script type="text/javascript">
  document.querySelectorAll("#job_number").forEach(function(node) {
    node.ondblclick = function() {
      var val = this.innerHTML;
      let n = this.attributes[1].nodeValue; //[1].name;


      var hidden = document.createElement("input");
      hidden.value = "update_job";
      hidden.name = "update_job";
      hidden.type = "hidden";

      var input = document.createElement("input");
      input.value = val;
      input.name = 'job_number';

      input.onchange = function() {
        let elements = document.querySelectorAll("#process");
        elements.forEach(e => {
          e.name = "";

        })
        this.form.submit();
      }

      input.onblur = function() {
        var val = this.value;
        this.parentNode.innerHTML = val;

      }
      this.innerHTML = "";
      this.appendChild(hidden);
      this.appendChild(input);
      input.focus();
    }
  });
</script>