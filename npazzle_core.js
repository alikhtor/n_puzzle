const fs = require('fs');

global.map = [];
global.mapSize = 0;
global.result = {};
global.mode = "";

function logMyErrors(error){
    console.log(error);
}

async function main(){
    try {
        validate();
        checkOnSolving();
        findNewWay();
        formatNewWay();

        Board.initBoard();
        Solver.initSolve(Board);
        Solver.solve();
    } 
    catch (e) {
        logMyErrors(e);
    }
}

let Solver = {
    board: {},
    initSolve(Board){
        this.board = Board;
    },
    solve(){
        
    }
}


let Board = { 
    depth : 0,
    zeroX : 0,
    zeroY : 0,

    zeroReset() {
        this.zeroX = 0;
        this.zeroY = 0;
    },

    checkOrder(x1, x2, k1, k2, i){
        let fx1 = x1.indexOf(global.newMap[i]);
        if (fx1 >= 0) {
            let fx2 = x2.indexOf(global.newMap[i]);
            if (fx2 >= 0){
                if (fx1 > fx2){
                    if (k1 < k2) return 0;
                }
                else if (fx1 < fx2){
                    if (k1 > k2) return 0;
                }
            }
        }
        return 1;
    },
    linearConflict(row, cell, keyX, keyY){
        let depthDelta = 0;
        for (let k = keyY; k < global.oldMap.count; k++)
            if (!this.checkOrder(cell, row[k], keyX, k, keyY));
                depthDelta++;
    
        return depthDelta;
    },

    wpMode(){
        this.depth = 0;
        global.oldMap.forEach( (row, keyX) => {
            row.forEach( (cell, keyY) => {
                if (global.newMap[ keyX ][ keyY ] != cell) this.depth++;
            });
        });
    },

    initBoard(){ 
        console.log("kek");
        
        global.oldMap.forEach( (row, keyX)=> {
            row.forEach( (cell, keyY) => {
                if (global.newMap[ keyX ][ keyY ] != cell){
                    this.depth += Math.abs(this.getX(cell) - keyX) + Math.abs(this.getY(cell) - keyY);
                }
                if (cell == 0) this.zeroReset();
                if (global.mode == '-lc') this.depth += this.linearConflict(row, cell, keyX, keyY);
            });
            if (global.mode == '-wp') wpMode();
        });
        console.log(this.depth);
    },
    getX(xVal){
        for(var x = 0; x < global.newMap.length; x++ ){
            for(var y = 0; y < global.newMap.length; y++ ){
                if (global.newMap[x][y] == xVal) return x;
            }
        }
    },
    getY(yVal){
        for(var x = 0; x < global.newMap.length; x++ ){
            for(var y = 0; y < global.newMap.length; y++ ){
                if (global.newMap[x][y] == yVal) return y;
            }
        }
    }
};


function formatNewWay(){
    let way = Object.values(global.result);
    let result = [];

    let i = -1;
    way.forEach( (el, key) => {
        if (key % global.mapSize == 0) {
            i++;
            result[i] = new Array(); 
        }
        result[i].push(el);
    })
    global.newMap = result;
}

function findNewWay(){
    let count = {
        side: 1,
        gp: 1
    };

    let data = {
        position : -1,
        side : global.mapSize
    }
    
    const findSmaller = () => {
        let matrix = {x:0, y:0};
        let size_map = smaller = global.mapSize * global.mapSize;
        global.map.forEach((row, x) => {
            row.forEach((cell, y) => {
                if (cell < smaller && cell > 0) {
                    smaller = cell;
                    matrix.x = x;
                    matrix.y = y;
                }
            });
        });
        delete global.map[matrix.x][matrix.y];
        if (smaller == size_map) return 0;
        return smaller;
    }

    const totalCount =  global.mapSize * global.mapSize;
    for(var x = 0; x < totalCount;) {
        for(var y = 0; y < data.side; y++) {
            if (count.side == 1) data.position++;
            if (count.side == 2) data.position += global.mapSize;
            if (count.side == 3) data.position--;
            if (count.side == 4) data.position -= global.mapSize;

            let t1 = findSmaller();
            global.result[data.position] = {};
            global.result[data.position] = t1;
            x++;
        }

        if (count.gp == 3 || (count.gp == 1 && data.side == global.mapSize)){
            count.gp = 1;
            data.side--;
        }

        count.gp++;
        count.side++;

        if (count.side == 5) count.side = 1;
        if (data.side == -1) break;
    }
}

function checkOnSolving(){
    let data = {
        zeroRow:0,
        checkSum:0,
        direction:[]
    };

    global.map.forEach( (el, key) => {  
        if (el.indexOf(0) != -1) data.zeroRow = (key + 1) % 2 ? key + 1 : key;
    });

    global.map.forEach( (row, x) => {
        row.forEach( (el, y) => {
            let step = 0;
            data.direction.forEach(directionEl => {
                step += el > directionEl ? 1 : 0;
            })
            data.direction.push(el);
            data.checkSum += el - step + data.zeroRow;
        });
    });

    if ( (data.checkSum % 2) != 0) {
        throw 'unsolvable puzzle';
    } 
}

function  validate(plainMap) {
    if (process.argv.length < 4) {
        throw 'incorrect count of arguments';
    }
    global.mode = process.argv[2];
    var fileData = fs.readFileSync(process.argv[3], "utf8");
    plainMap = fileData.split('\n');
    
    plainMap.forEach( (el, key) => {
        if (el[0] == '#') return;
        el = el.includes("#") ? el.substring(0, el.indexOf("#")) : el;
        let row = el.replace(/\s\s+/g, ' ').trim();
        row = row.split(' ').map(x=>+x);
        if (row.length == 0) return;
        if (row.length == 1){
            let size = parseInt(row[0]);
            if (size > 0) global.mapSize = size;
            return ; 
        }
        if (row.length != global.mapSize) throw 'invalid map';

        global.map.push(row); 
    });
    global.oldMap = JSON.parse(JSON.stringify(global.map));
}

main()