"use strict";
/* 
 * Questa classe permette
 */

/*
 * Usa la stessa convenzione di strcmp:
 * Farò così:
 *    -1: x<y
 *     0: x == y
 *    +1: x>1
 */
var COL_TYPES = {
    numeric: (x,y) => {
        if(x == y) return 0;

        if(isNaN(parseFloat(y))) return -1;
        if(isNaN(parseFloat(x))) return 1;
        return parseFloat(x) < parseFloat(y) ? -1 : +1;
    },
    
    string: (x,y) => {
        if(x == y) return 0;
        return x.toLocaleLowerCase() < y.toLocaleLowerCase() ? -1 : +1;
    }
};

function tOrdinatore(table)
{
    if(table.tagName !== "TABLE")
    {
        console.error("tOrdinatore richiede un oggetto table");
        throw new Error("InvalidArgumentException");
    }
    this.table = table;
    var thead = table.tHead;
    if(!thead)
    {
        console.error("table deve avere un thead");
        throw new Error("InvalidArgumentException");
    }
    if(table.tBodies.length !== 1)
    {
        console.error("table deve avere un unico");
        throw new Error("InvalidArgumentException");
    }
    var tbody = table.tBodies[0];
    var sorter = (e) => {
        var th = e.target;
        var index = th.cellIndex;
        var type = th.dataset.type;
        var swapper = (dir) => dir === "up"? "down" : "up";
        var dir = undefined;
        if(th.dataset.direction !== undefined) {
            dir = th.dataset.direction;
        } else {
            dir = "up";
        }
        th.dataset.direction = swapper(dir);
        
        var comparator = COL_TYPES[type];
        
        var li = [];
        for(var el of tbody.rows)
            li.push({index: el.cells[index].dataset.val, elem: el});
        li.sort((a,b) => comparator(a.index, b.index));
        
        switch (dir) {
            case "down":
                while (li.length !== 0) {
                    var row = li.pop().elem;
                    tbody.appendChild(row);
                }
                break;
            case "up":
                while (li.length !== 0) {
                    var row = li.shift().elem;
                    tbody.appendChild(row);
                }
                break;
            default:
                console.error("Impossibile riordinare la tabella!");
                throw new Error("FatalErrorException");
                break;
        }
        console.info("Tabella riordinata con successo.");
    };
    for(var th of thead.rows[0].cells)
    {
        th.addEventListener("click", sorter);
    }
}

