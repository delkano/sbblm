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
    row += "<th><input class='number' type='number' name='player["+nb+"][number]' value="+nb+" readonly='readonly'/></th>";
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
    row.find("input").change(calculateValue);
    row.find("select").change(calculateValue);
    $("#race_hidden").val($("#race").val());
    $("#race").prop("disabled", "disabled");
    row.find(".basicskills").selectivity({readOnly: true});
    row.find(".learnedskills").selectivity();
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
    var lines = [];
    data.skills.forEach( (skill) => {
        lines.push({id: skill.id, text: skill.name+" => "+skill.desc});
    });
    bs.selectivity("data", lines);
    bs.selectivity("setOptions", {items: lines});

    var ls = row.find('.learnedskills');
    lines = [];
    row.find('.value').val(+data.value);

    var singles = [];
    data.basic.forEach( (group) => {
        var groupObj = [];
        skills[group.name].forEach( (skill) => {
            groupObj.push({id: skill.id, text: skill.name+" => "+skill.desc});
        });
        singles.push({ text: group.name, children: groupObj });
    });
    lines.push({ text: "{{@L.player.singles}}", children: singles });

    var doubles = [];
    data.doubles.forEach( (group) => {
        var groupObj = [];
        skills[group.name].forEach( (skill) => {
            groupObj.push({id: skill.id, text: skill.name+" => "+skill.desc});
        });
        doubles.push({ text: group.name, children: groupObj });
    });
    lines.push({ text: "{{@L.player.doubles}}", children: doubles });
    ls.selectivity("setOptions", {items: lines});

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
function findSkillGroup(id) {
    for(var group in skills) 
        for(var i=0; i<skills[group].length; i++) 
            if(id === skills[group][i].id) return group;
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
    if(row.target) { // We've been called from an event
        row = $(row.target).parent().parent();
    } else {
        row = $(row);
    }
    var pos = findPositionById(row.find(".positions").val());
    if(!pos) return; // Bad row
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

    // This reduces value when injured; a house rule I like but is not official.
    // It should be (although this doesn't take into account an increased and then injured stat):
    // value+= (ma>pos.MA? (ma - pos.MA) * 20:0) + (av>pos.AV?(av - pos.AV) * 20:0);
    // value+= (st>pos.ST? (st - pos.ST) * 50:0) + (ag>pos.AG?(ag - pos.AG) * 40:0);
    value+= (ma - pos.MA) * 20 + (av - pos.AV) * 20;
    value+= (st - pos.ST) * 50 + (ag - pos.AG) * 40;

    // For each skill, we need to find out if it's singles (20K) or doubles (30K).
    // There's room for improvement here, too many nested for loops. There aren't yet many levelups,
    // so it's still manageable, but it doesn't scale well.
    var skills = row.find(".learnedskills").selectivity("data");
    if(skills !== null) 
        skills.forEach( skill => {
            if(pos.basic.indexOf(findSkillGroup(skill.id)) !== -1)
                value+= skills.length * 20;
            else
                value+= skills.length * 30;
        });

    var levelUpped = 1; // Let's calculate how many upgrades we've already applied
    levelUpped+= (ma - pos.MA > 0)? ma - pos.MA : 0;
    levelUpped+= (av - pos.AV > 0)? av - pos.AV : 0;
    levelUpped+= (ag - pos.AG > 0)? ag - pos.AG : 0;
    levelUpped+= (st - pos.ST > 0)? st - pos.ST : 0;
    levelUpped+= skills?skills.length:0;

    row.find(".value").val(value);

    // We only allow to add skills and attributes if we've leveled up.
    if(levelUpped < level) { // We haven't upgraded everything we could
        row.addClass("needsUpgrade");
        blockFields(row, false);
    } else {
        row.removeClass("needsUpgrade");
        blockFields(row, true);
    }

    calculateTotalValue();
}

function blockFields(row, value) {
    row.find(".learnedskills").selectivity("setOptions", {"removeOnly": value});
    var ma = row.find(".ma");
    ma.attr("max", value?ma.val():+(ma.data("init") + 1));
    var ag = row.find(".ag");
    ag.attr("max", value?ag.val():+(ag.data("init") + 1));
    var st = row.find(".st");
    st.attr("max", value?st.val():+(st.data("init") + 1));
    var av = row.find(".av");
    av.attr("max", value?av.val():+(av.data("init") + 1));
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

    // Let's change a few templates to add the hover tooltips to skills
    var templates = $.Selectivity.Templates;
    templates.multipleSelectedItem = function(options) {
        var extraClass = options.highlighted ? ' highlighted' : '';
        var texts = options.text.split(" => ");
        return (
            '<span class="selectivity-multiple-selected-item skill-tag' +
            extraClass +
            '" ' +
            'data-item-id="' +
            escape(options.id) +
            '" data-tooltip="'+
            texts[1] +
            '">' +
                '<a class="selectivity-multiple-selected-item-remove">' +
                  '<i class="fa fa-remove"></i>' +
                 '</a>' +
            texts[0] +
            '</span>'
        );
    };
    templates.resultItem = function(options) {
        var texts = options.text.split(" => ");
        return (
            '<div class="selectivity-result-item' +
            (options.disabled ? ' disabled' : '') +
            '"' +
            ' data-item-id="' +
            escape(options.id) +
            '">' +
            texts[0] +
            '</div>'
        );
    };
    $(".basicskills").selectivity({"readOnly": true});
    $(".learnedskills").selectivity();

    var id = $("#race").val();

    $.get("/skills/getlist", 
        null,
        data => {
            skills = data;
            if(id) {
                $.get("/race/"+id+"/getlist",
                    null,
                    (data) => {
                        positions = data.positions; 
                        reroll_cost = data.rerolls;

                        // Let's recalculate all player values before we go any further
                        $(".player tr").each( function(i, row) {
                            calculateValue(row);
                        });
                    },
                    'json'
                );
            }
        },
        'json'
    );


    teammoney = $('#money').val();
    cheerleaders = $('#cheerleaders').val();
    assistants = $('#assistants').val();
    apothecary = $('#apothecary').val();
    rerolls = $('#rerolls').val();



    /*
     * this swallows backspace keys on any non-input element.
     * stops backspace -> back
     */
    var rx = /INPUT|SELECT|TEXTAREA/i; //Excluded tags
    var cx = /trumbowyg-editor/i; //Excluded classes

    $(document).bind("keydown", function(e){
        if( e.which == 8 ){ // 8 == backspace
            if((!rx.test(e.target.tagName) && !cx.test(e.target.className)) || e.target.disabled || e.target.readOnly || $(e.target).is(':checkbox,:radio,:submit') ){
                e.preventDefault();
            }
        }
    });
})
