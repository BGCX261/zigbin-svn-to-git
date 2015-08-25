
function zig_droplist_filter(droplist_filter_id,droplist_id,droplist_options_string)
{var selected_filter=document.getElementById(droplist_filter_id).options[document.getElementById(droplist_filter_id).selectedIndex].text;var first_character="";var counter=document.getElementById(droplist_id).length;while(counter>0)
{counter--;document.getElementById(droplist_id).remove(counter);}
var droplist_options=droplist_options_string.split(",");var droplist_options_length=droplist_options.length;counter=0;while(counter<droplist_options_length)
{first_character=droplist_options[counter].substr(0,1);if(selected_filter==first_character.toLowerCase()||(droplist_options[counter]==""&&counter==0)||selected_filter=="all"||selected_filter=="")
{var new_option=document.createElement('option');new_option.text=droplist_options[counter];try
{document.getElementById(droplist_id).add(new_option,null);}
catch(ex)
{document.getElementById(droplist_id).add(new_option.text);}}
counter++;}}