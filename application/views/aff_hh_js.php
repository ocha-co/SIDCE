<?php
/**
 * Report js file.
 *
 * Handles javascript stuff related to report submit function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>		

//Check max rows number to doc
function check_num(){
    var max_num = 20;
    if ($('tr.aff_tr').length > max_num)  return false;
    else                                            return true;
}

function copyHead(){

    var num = $('tr.aff_tr_head').length - 1;

    var l = $('tr.aff_tr_head:first').clone(true);

    // Show and Change radio buttons names
    l.removeClass('hide')
    .find(":checkbox")
    .each(function(){
        $(this).attr('name', $(this).attr('name') + '_' + num);
    });
    
    $('.tr_submit').before(l);
}

function validate(){
    
    var r = true;
    var msg = 'Los siguientes campos son obligatorios:\n\n';
    
    var req = new Array('category_id', 'aff_hh_location', 'aff_hh_date');
    var req_l = new Array('<?php echo Kohana::lang('ui_main.event_type')?>','<?php echo Kohana::lang('ui_main.aff_hh_label_1')?>','<?php echo Kohana::lang('ui_main.date')?>');
    var req_n = req.length;

    for (var i=0;i<req_n;i++){
        if ($('#' + req[i]).val() == ''){
            r = false;
            msg += '- ' + req_l[i] + '\n';
        }
    }
    
    if (!r) alert(msg);

    return r;
}

$(function(){

    // Validate
    $('#submit').click(function(){ return validate(); });

    $("#aff_hh_date").datepicker({ 
        showOn: "both", 
        buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
        buttonImageOnly: true,
        dateFormat: 'yy-mm-dd'
    });

    // Add row highlight on city dropdown
    $('select#city_id:first').each(function(){ 
        $(this).mousedown(function(){
            $('tr.aff_tr').each(function(){ $(this).removeClass('highlight')});
            $(this).closest('tr').addClass('highlight');
        });

        $(this).change(function(){
            var dd = $(this);
            if (dd.val() != ''){
                // Populate township dropdown
                $.getJSON("<?php echo url::base() ?>township/dropdown/" + $(this).val(), function(result) {
                    var optionsValues = '<select name="township_id[]" class="township">';
                    $.each(result, function(i, item) {
                        optionsValues += '<option value="' + item.id + '">' + item.name + '</option>';
                        });
                    optionsValues += '</select>';
                    
                    var options = dd.closest('td').next().children();
                    console.log(dd.closest('td').next().children());
                    options.replaceWith(optionsValues);
                });
            }
        })
    });
    
    // First head
    copyHead();
    
    // New Head action
    $('#new_h').click(function(){
        if (check_num()) copyHead();
    });
    
    // New Member action
    $('#new_m').click(function(){
        if (check_num()) $('.tr_submit').before($('tr.aff_tr_member:first').clone(true).removeClass('hide'));
    });
    
    // Remove Member action
    $('#rem_m').click(function(){
        $(this).closest('tr').remove();
    });
});

