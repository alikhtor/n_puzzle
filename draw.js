export function draw(val){
    var canvas = document.getElementById('roll-block');
    var maxLen = 4;
    let style = ['rgb(200,0,0)', 'rgb(20,20,20)'];
    let x0 = 10, y0=10, count = 1;
    let step = 30;
    let map;
    if (val == true) {
        map = [
            [8, 9, 7, 12],
            [0, 2, 4, 1],
            [14, 10, 13, 6],
            [11, 15, 3, 5]
        ];
    } else {
        map = [
            [1, 2, 3, 4],
            [5, 6, 7, 8],
            [9, 10, 11, 12],
            [13, 14, 15, 16]
        ];        	
    }
    if (canvas.getContext) {
        console.log(41);
        var ctx = canvas.getContext("2d");

        for(var x = 0; x < maxLen; x++){
            x0 = 10;
            for(var y = 0; y < maxLen; y++){
                if (y == 0) y0 += step * 1.3;
                x0 += step * 1.25; 
                ctx.fillStyle = style[x%2? 1 - (y % 2) : y % 2]; 

                ctx.fillStyle = 'rgb(' + Math.floor(255 - 42.5 * x) + ', ' + Math.floor(255 - 42.5 * y) + ', 0)';

                ctx.fillRect (x0, y0, step, step);

                ctx.fillStyle = 'white';
                ctx.font = "22px arial";
                ctx.fillText(map[x][y], x0, y0 + step * 0.75);

                count++;
            }
        }
    }
}