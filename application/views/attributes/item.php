
<div class="form-group form-group-sm">
	<?php echo form_label($this->lang->line("attributes_definition_name"), "definition_name_label", array('class' => 'control-label col-xs-3')); ?>
    <div class='col-xs-8'>
		<?php echo form_dropdown('definition_name', $definition_names, -1, array('id' => 'definition_name', 'class' => 'form-control')); ?>
    </div>

</div>

<?php

foreach($definition_values as $definition_id => $definition_value)
{
    ?>

<div class="form-group form-group-sm">
    <?php echo form_label($definition_value['definition_name'], $definition_value['definition_name'], array('class' => 'control-label col-xs-3')); ?>
    <div class='col-xs-8'>
        <div class="input-group">

            <?php
            $attribute_value = $this->Attribute->get_attribute_value($item_id, $definition_id);
            $attribute_id = (empty($attribute_value) || empty($attribute_value->attribute_id)) ? NULL : $attribute_value->attribute_id;
            echo form_hidden("attribute_ids[$definition_id]", $attribute_id);

            if ($definition_value['definition_type'] == DATE)
            {
                echo form_input(array(
                    'name' => 'attribute_links[$definition_id]',
                    'value' => date($this->config->item('dateformat') . ' ' . $this->config->item('timeformat'), strtotime($definition_value['attribute_value'])),
                    'class' => 'form-control input-sm',
                    'data-definition-id' => $definition_id,
                    'readonly' => 'true'));
            }
            else if ($definition_value['definition_type'] == DROPDOWN)
            {
                $values = $this->Attribute->get_definition_values($definition_id);
                $selected_value = $this->Attribute->get_link_value($item_id, $definition_id);
                echo form_dropdown("attribute_links[$definition_id]", $values, (empty($selected_value) ? NULL : $selected_value->attribute_id), "class='form-control' data-definition-id='$definition_id'");
            }
            else if ($definition_value['definition_type'] == TEXT)
            {
                $value = (empty($attribute_value) || empty($attribute_value->attribute_value)) ? NULL : $attribute_value->attribute_value;
                echo form_input("attribute_links[$definition_id]", $value, "class='form-control' data-definition-id='$definition_id'");
            }
            ?>
            <span id="remove_attribute_link" class="input-group-addon input-sm btn btn-default"><span class="glyphicon glyphicon-trash"></span></span>
        </div>
    </div>
</div>

    <?php
}
?>

<script type="text/javascript">
    (function() {
        $("#remove_attribute_link").click(function() {
            $(this).parents(".form-group").remove();
        });

        $("input[name*='attribute_links']").change(function() {
            var definition_id = $(this).data('definition-id');
            $("input[name='attribute_ids[" + definition_id + "]").val("");
        }).autocomplete({
            source: function(request, response) {
                $.get('<?php echo site_url('attributes/suggest_attribute/');?>' + this.element.data('definition-id') + '?term=' + request.term, function(data) {
                    return response(data);
                }, 'json');
            },
            appendTo: '.modal-content',
            select: function (event, ui) {
                event.preventDefault();
                $(this).val(ui.item.label);
            },
            delay:10
        });

        var definition_values = function() {
            var result = {};
            $("input[name*='attribute_links'").each(function(index, element) {
                var definition_id = $(this).data('definition-id');
                result[definition_id] = $(element).val();
            });
            return result;
        }

        $("#definition_name").change(function() {
            var attribute_values = definition_values();
            var definition_id = $("option:selected", this).val();
            attribute_values[definition_id] = "";
            $("#attributes").load('<?php echo site_url("items/attributes/$item_id");?>', {
                'definition_ids': JSON.stringify(attribute_values)
            });
        });

    })();
</script>


