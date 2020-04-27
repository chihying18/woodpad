
     function drawRadar(divId, resolution, brightness, colour, portability ,adjustability,lamp, audio, connecivity) {
     var context = document.getElementById(divId).getContext('2d');
   
           //设置宽高一定要在canvas节点添加之后
     document.getElementById(divId).width = document.body.clientWidth/2;
     document.getElementById(divId).height = document.body.clientWidth/2.3;
     var scalesize = document.body.clientWidth/400;


     if(document.body.clientWidth<400){
     document.getElementById(divId).width = 300;
     document.getElementById(divId).height = 300;
      scalesize = 1.5;
     }
      context.translate(scalesize*45,scalesize*24);
      context.scale(scalesize,scalesize);   //context.scale(scalewidth,scaleheight)

      // draw line
      context.strokeStyle = 'gray'; // set the strokeStyle color to 'navy' (for the stroke() call below)
      context.lineWidth = '.2';
      context.beginPath();          // start a new path
      context.moveTo(-10,50); 
      context.lineTo(110,50); 

      context.moveTo(50,-10); 
      context.lineTo(50,110); 
      
      context.moveTo(10,10);  //audio to Portability
      context.lineTo(90,90); 
      
      context.moveTo(90,10); //Brightness to Lamp life
      context.lineTo(10,90); 
      context.stroke();

      
      // draw background
      context.strokeStyle='gray';
      context.beginPath();
      context.arc(50,50,50,0,Math.PI*2,true); // Outer circle
      context.arc(50,50,40,0,Math.PI*2,true); // Outer circle
      context.arc(50,50,30,0,Math.PI*2,true); // Outer circle
      context.arc(50,50,20,0,Math.PI*2,true); // Outer circle
      context.arc(50,50,10,0,Math.PI*2,true); // Outer circle
      context.stroke();
      //arc(x, y, radius, startAngle, endAngle, anticlockwise)
      context.closePath();

      // draw wording
      context.font = "6px Open Sans";
      context.fillText("Resolution",35,-15);
      context.fillText("Brightness",90,5);
      context.fillText("Colour",115,52);
      context.fillText("Portability",90,100);
      context.fillText("Adjustability",35,120);
      context.fillText("Lamp life",-15,100);
      context.fillText("Audio",-30,52);
      context.fillText("Connecivity",-15,5);

      
      // draw fill
     //   context.strokeStyle = 'red'; //debug use
      //  context.lineWidth = '3'; //debug use

      context.beginPath();   
    
      //center 50,50

      resolution =  50 - resolution*10;

      brightnessl = brightness*10 + 35;
      brightnessr = 100 - brightnessl;

      colour = colour*10 + 50;
      portability = portability*10 + 40;
      adjustability = adjustability*10 + 50;

      lampr = lamp*10 + 38;
      lampl = 100 - lampr;

      audio = 50 - audio*10;

      connecivity = 60 - connecivity*10;


          context.moveTo(50,resolution); //Resolution
          context.lineTo(brightnessl,brightnessr); //Brightness * 90/10
          context.lineTo(colour,50); //Colour
          context.lineTo(portability, portability); //portability
          context.lineTo(50, adjustability); //Adjustability
          context.lineTo(lampl, lampr); //Lamp life
          context.lineTo(audio, 50); //Connecivity 
          context.lineTo(connecivity, connecivity); //Audio

      context.stroke();
      context.closePath();
      
      // context.fillStyle='#84cec0';
      context.fillStyle='rgba(153, 0, 0, 0.85)';
      context.fill();  

}

