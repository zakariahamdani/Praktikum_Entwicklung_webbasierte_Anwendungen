let request = new XMLHttpRequest();



window.onload = function () {
    //document.getElementsByTagName("pizzaElement").addEventListener ("click", addPizza(), false);
    //document.getElementById("submitButton").disabeld = true;
}
//let sum = 0;

function addPizza(pizzaId, pizzaName, PizzaPrice) {
    //alert("Hellos"+pizza);
    let option = document.createElement("option");
    option.value = pizzaId;
    option.text = pizzaName;
    //
    document.getElementById("ordered_Pizzas").add(option);


    let sum = parseFloat(document.getElementById("total").innerHTML) + parseFloat(PizzaPrice);
    document.getElementById("total").textContent = sum.toFixed(2);

    checkConditions();
}

function selectPizzas() {
    options = document.getElementsByTagName("option");
    	for ( i=0; i<options.length; i++)
    	{
    		options[i].selected = "true";
    	}
}

function deleteSelection() {
    select = document.getElementById("ordered_Pizzas");
    options = select.getElementsByTagName("option");
    var sum = parseFloat(document.getElementById("total").textContent);
    for ( i=0; i<options.length; i++)
    {   
        if(options[i].selected == true){
            let value = options[i].value;
            let price = parseFloat(document.getElementById(value).innerHTML);
            sum = parseFloat(sum.toFixed(2)) - price;
            document.getElementById("total").textContent = sum.toFixed(2);
            select.remove(i);
            i--;
        }
        //select.remove(select.selectedIndex);
    }
    checkConditions();
}

function deleteAll() {
    select = document.getElementById("ordered_Pizzas");
    options = select.getElementsByTagName("option");
    
    for ( i=0; i<options.length; i++)
    {   
            select.remove(i);
            i--;
        //select.remove(select.selectedIndex);
    }
    document.getElementById("total").innerHTML = "00.00";
    checkConditions();
}

function checkConditions() {
    select = document.getElementById("ordered_Pizzas");
    options = select.getElementsByTagName("option");
    adresse = document.getElementById("address")

    if(adresse.value !="" && options.length > 0)
        document.getElementById("submitButton").disabled = false;
    else
        document.getElementById("submitButton").disabled = true;
}


