var positions=[]; // We need to store them for the time being
var skills={}; //Ditto with the skill lists
var teammoney; // Initial Money amount when you load the team
var cheerleaders;
var assistants;
var apothecary;
var rerolls;
var reroll_cost = 0;

function addPlayer() {
    var nb = $(".player.list tr").length;
    if(nb > 16) { $('.add-player').disable(); }
    var nbs = $('.player.list tr .number').map( (i, e) => { return parseInt($(e).val()); }).get().sort( (a,b) => { return a-b; } );
    switch(nbs.length) {
        case 0: nb = 1; break;
        case 1: nb = nbs[0]==1?2:1; break;
        default:
                for(var n=1; n< nbs.length; n++) {
                    if(nbs[n] - nbs[n-1]> 1) {
                        nb = nbs[n-1] + 1;
                    }
                }
    }
    var row = "<tr class='new'>";
    row += "<td><input class='number' type='number' name='player["+nb+"][number]' value="+nb+" readonly='readonly'/></td>";
    row += '<td><input class="name" name="player['+nb+'][name]" required=true/></td>';
    row += "<td><select class='positions' name='player["+nb+"][position]' required=true>";
    row += '<option value="" disable selected hidden> --- </option>';
    positions.forEach( (p) => row+= "<option value='"+p.id+"'>"+p.name+"</option>");
    row += "</select></td>";
    row += '<td><input class="ma" name="player['+nb+'][ma]" type="number" value="" required=true/></td>';
    row += '<td><input class="st" name="player['+nb+'][st]" type="number" value="" required=true/></td>';
    row += '<td><input class="ag" name="player['+nb+'][ag]" type="number" value="" required=true/></td>';
    row += '<td><input class="av" name="player['+nb+'][av]" type="number" value="" required=true/></td>';
    row += "<td><select class='basicskills' name='player["+nb+"][basicskills][]' multiple=true readonly='readonly'>";
    row += "</select></td>";
    row += "<td><select class='learnedskills' name='player["+nb+"][learnedskills][]' multiple=true>";
    row += "</select></td>";
    row += '<td><input class="cp" name="player['+nb+'][cp]" type="number" value="0" required=true/></td>';
    row += '<td><input class="td" name="player['+nb+'][td]" type="number" value="0" required=true/></td>';
    row += '<td><input class="int" name="player['+nb+'][int]" type="number" value="0" required=true/></td>';
    row += '<td><input class="cas" name="player['+nb+'][cas]" type="number" value="0" required=true/></td>';
    row += '<td><input class="mvp" name="player['+nb+'][mvp]" type="number" value="0" required=true/></td>';
    row += '<td><input class="spp" name="player['+nb+'][spp]" type="number" value="0" required=true/></td>';
    row += '<td><input class="value" name="player['+nb+'][playervalue]" type="number" value="" readonly="readonly"/></td>';
    row += '<td><input class="level" name="player['+nb+'][level]" type="number" value="1" required=true/></td>';
    row += '<td><input class="hurt" name="player['+nb+'][hurt]" type="checkbox" /></td>';
    row += '<td><textarea class="notes" name="player['+nb+'][comment]"></textarea></td>';
    row += '<td><input class="dead" name="player['+nb+'][dead]" type="checkbox" /></td>';
    row += '<td><button type="button" class="remove-player">{{@L.basic.delete}}</button></td>';

    row += "</tr>";
    row = $(row.trim());
    $(".player.list").append(row);
    row.find(".positions").change(selectPosition);
    row.find(".remove-player").click(removePlayer);
    row.find(".basicskills").chosen({width: '10em'});
    row.find(".learnedskills").chosen({width: '10em'});
    $("#race_hidden").val($("#race").val());
    $("#race").prop("disabled", "disabled");
}
function selectRace(e) {
    var id = this.value;
    $.get("/race/"+id+"/getlist",
            null,
            populatePositions,
            'json'
         );
}
function selectPosition(e) {
    var id = e.target.value;
    var data = findPositionById(id);
    var row = $(e.target).parent().parent();
    row.find('.ma').val(+data.MA);
    row.find('.st').val(+data.ST);
    row.find('.ag').val(+data.AG);
    row.find('.av').val(+data.AV);
    var bs = row.find('.basicskills');
    bs.find("option").remove();
    data.skills.forEach( (skill) => {
        bs.append("<option value='"+skill.id+"' selected=true>"+skill.name+"</option>");
    });
    bs.trigger('chosen:updated');

    var ls = row.find('.learnedskills');
    ls.find("option").remove();
    line = "";
    row.find('.value').val(+data.value);

    if(Object.keys(skills).length === 0) {
        $.get("/skills/getlist",
                null,
                (d) => { 
                    skills = d; 
                    data.basic.forEach( (group) => {
                        line += "<optgroup label='"+group.name+"'>";
                        skills[group.name].forEach( (skill) => {
                            line += "<option value='"+skill.id+"'>"+skill.name+"</option>";
                        });
                        line += "</optgroup>";
                    });
                    // Separated because, in ideal conditions, we'd have nested optgroups to
                    // separate basic skill ups from doubles. I can't find a way to cleanly do this on HTML yet.
                    data.doubles.forEach( (group) => {
                        line += "<optgroup label='"+group.name+"'>";
                        skills[group.name].forEach( (skill) => {
                            line += "<option value='"+skill.id+"'>"+skill.name+"</option>";
                        });
                        line += "</optgroup>";
                    });
                    ls.append(line);
                    ls.trigger('chosen:updated');
                },
                'json'
             );
    } else {
        data.basic.concat(data.doubles).forEach( (group) => {
            line += "<optgroup label='"+group.name+"'>";
            skills[group.name].forEach( (skill) => {
                line += "<option value='"+skill.id+"'>"+skill.name+"</option>";
            });
            line += "</optgroup>";
        });
        ls.append(line);
        ls.trigger('chosen:updated');
    }
    calculateMoney();
}

function calculateMoney() {
    // Calculate total money left
    var cost = 0;
    $(".new .value").each( (i, v) => { cost+= +$(v).val(); });
    {~ if(@cfg.ffPrice != -1): ~}
    cost += (+$("#ff").val() - {{@cfg.ff}}) * {{@cfg.ffPrice}};
    {~ endif ~}

    cost+= -(+cheerleaders - +$('#cheerleaders').val()) * 10;
    cost+= -(+assistants - +$('#assistants').val()) * 10;
    cost+= -(+apothecary - +$('#apothecary').val()) * 50;
    {~ if(count(@team.hosted) >0 || count(@team.visited) >0): ~}
            cost+= -(+rerolls - +$('#rerolls').val()) * 2 * reroll_cost;
            {~ else: ~}
            cost+= -(+rerolls - +$('#rerolls').val()) * reroll_cost;
    {~ endif ~}

    $('#money').val( teammoney - cost);
    calculateTotalValue();
}

function findPositionById(id) {
    var ret = $.grep(positions, function(e) {return e.id == id});
    return ret[0];
}

function populatePositions(data) {
    positions = data.positions; 
    reroll_cost = data.rerolls;
    $(".positions").each((i, p) => {
        $(p).find("option").remove();
        $(p).append( '<option value="" disable selected hidden> --- </option>');
        data.forEach( (d) => {
            $(p).append("<option value='"+d.id+"'>"+d.name+"</option>"); 
        });
    });
}

function removePlayer(e) {
    var row = $(e.target).parent().parent();
    row.remove();
    calculateMoney();

    //We need here some code to cancel #race disabled status when there are no players left
}

function calculateValue(row) {
    row = $(row.target).parent().parent();
    var pos = findPositionById(row.find(".positions").val());
    var value = pos.value;

    // Let's calculate SPP's too
    var td = row.find(".td").val(),
        cp = row.find(".cp").val(),
        int = row.find(".int").val(),
        cas = row.find(".cas").val(),
        mvp = row.find(".mvp").val();
    var spp = +cp + +td * 3 + +int * 2 + +cas * 2 + +mvp * 5;
    row.find(".spp").val(spp);
    // Let's calculate the level then
    var level;
    if(spp <= 5) 
        level = 1;
    else if(spp <= 15)
        level = 2;
    else if(spp <= 30)
        level = 3;
    else if(spp <= 50)
        level = 4;
    else if(spp <= 75)
        level = 5;
    else if(spp <= 175)
        level = 6;
    else level = 7;
    row.find(".level").val(level);

    // Trait improvements
    var ma = row.find(".ma").val(),
        st = row.find(".st").val(),
        ag = row.find(".ag").val(),
        av = row.find(".av").val();

    value+= (ma - pos.MA) * 20 + (av - pos.AV) * 20;
    value+= (st - pos.ST) * 50 + (ag - pos.AG) * 40;

    var skills = row.find(".learnedskills").val();
    if(skills !== null) 
        value+= skills.length * 20;

    row.find(".value").val(value);
    calculateTotalValue();
}

function calculateTotalValue() {
    var total = 0;
    $('.player.list tr').each( (j, row) => {
        row = $(row);
        if(!row.find('td input[type=checkbox]').is(":checked")) 
            row.find('td .value').each( (i, v) => { total+= +$(v).val(); });
    });
    {~ if(@cfg.ffPrice != -1): ~}
    total+= (+$('#ff').val() - {{@cfg.ff}}) * {{@cfg.ffPrice}};
    {~ endif ~}
    total+= $('#cheerleaders').val() * 10;
    total+= $('#assistants').val() * 10;
    total+= $('#apothecary').val() * 50;
    total+= $('#rerolls').val() * reroll_cost;

    $('#value').val(total);
}

$(function(){
    $(".add-player").click(addPlayer);
    $(".change-positions").change(selectRace);
    $(".positions").change(selectPosition);
    $(".remove-player").click(removePlayer);

    $(".player.list input").change(calculateValue);
    $(".player.list select").change(calculateValue);
    $("#ff").change(calculateMoney);
    $("#cheerleaders").change(calculateMoney);
    $("#apothecary").change(calculateMoney);
    $("#rerolls").change(calculateMoney);
    $("#assistants").change(calculateMoney);

    $(".basicskills").chosen({width: '10em'});
    $(".learnedskills").chosen({width: '10em'});

    var id = $("#race").val();
    if(id) {
        $.get("/race/"+id+"/getlist",
                null,
                (data) => {
                    positions = data.positions; 
                    reroll_cost = data.rerolls;
                },
                'json'
             );
    }

    teammoney = $('#money').val();
    cheerleaders = $('#cheerleaders').val();
    assistants = $('#assistants').val();
    apothecary = $('#apothecary').val();
    rerolls = $('#rerolls').val();

    /*
     * this swallows backspace keys on any non-input element.
     * stops backspace -> back
     */
    var rx = /INPUT|SELECT|TEXTAREA/i;

    $(document).bind("keydown", function(e){
        if( e.which == 8 ){ // 8 == backspace
            if(!rx.test(e.target.tagName) || e.target.disabled || e.target.readOnly || $(e.target).is(':checkbox,:radio,:submit') ){
                e.preventDefault();
            }
        }
    });
})
