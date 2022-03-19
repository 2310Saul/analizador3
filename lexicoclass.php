<?php
class Lexer{

    protected $_lineas;
    protected $_numero;
    protected $_token;
    protected $_tokens = array();

    protected $_afd = array(
        0 => array(3, false, 1, false),
        1 => array(3, 3, 2, false),
        2 => array(3, false, false, false),
        3 => array(3, 3, false, true)
    );

    protected $_tokenList = array(
        //Simbolos
        " " => "ESPACIO",
        '"' => "COMILLA_DOBLE",
        "*" => "MULTIPLICACION",
        "+" => "SUMA",
        "-" => "RESTA",
        "/" => "DIVISION",
        "=" => "IGUAL",
        "(" => "ABRE PARENTESIS",
        ")" => "CIERRA PARENTESIS",
        //Reservadas
        "entero"  => "TIPO_DATO_ENTERO",
        "cadena"  => "TIPO_DATO_CADENA",
        "si"      => "ESTRUCTURA_CONDICIONAL_IF",
        "entonces" => "THEN",
        "escriba" => "ESCRITURA",
        "fin"     => "FIN_ESTRUCTURA_CONTROL"
    );

    protected $_delimitadores = ' "'; // Los delimitadores son ESPACIO y COMILLA_DOBLE
    
    function __construct($txt){
        $this->_lineas   = preg_split("/(\r\n|\n|\r)/", trim($txt));
 
        foreach($this->_lineas as $numero => $linea){
            $this->_numero = $numero;
            $this->lexico($linea);
        }

        $this->printTokens();
    }

    function lexico($linea){
        $tokens_line = new StringTokenizer($linea, $this->_delimitadores);
        foreach ($tokens_line as $pos => $tok) {
            $this->_token = $tok;
            $busqueda = $this->buscarExpresion();
            if($busqueda === false){
                if($this->esNumerico())
                    $this->_tokens[] =  $this->returnTokenItem("ENTERO");
                elseif($this->esIdentificador())
                    $this->_tokens[] = $this->returnTokenItem("IDENTIFICADOR");
                else
                    $this->_tokens[] = $this->returnTokenItem("ERROR");
            }else{
                $this->_tokens[] =  $this->returnTokenItem();
            }
        }
    }

    function buscarExpresion($c=null){
        if($c==null) $c = $this->_token;
        foreach($this->_tokenList as $exp => $name)
            if($exp == $c) return $name;
        return false;
    }

    function returnTokenItem($v=false){
        if($v==false) $v = $this->_tokenList;
        else $token =  $v;
        if(is_array($v)) $token = $this->buscarExpresion();
        return array(
            'lexema' => $this->_token,
            'token' => $token,
            'linea' => $this->_numero+1
        ); 
    }

    function esLetra($c=null){
        if($c==null) $c = $this->_token;
        $c = ord(strtolower($c));
        if($c >= 97 && $c <= 122) return true; //verificacion si es letra en ascci
        return false;
    }

    function esNumerico($c=null){
        if($c==null) $c = $this->_token;
        if(is_numeric($c)){
            $c = ord(strtolower((int)$c));
            if($c >= 48 && $c <= 57) return true; //verificacion si es numero en ascci
        }
        return false;
    }

    function esGuionBajo($c=null){
        if($c==null) $c = $this->_token;
        return ($c == "_");
    }

    function esIdentificador($c=null){
        if($c==null) $c = $this->_token;
        $transiciones = strlen($c);
        $i = 0; $estado = 0;
        while($i <= $transiciones){
            if($i==$transiciones) $entrada = 3; // COLUMNA DE ACEPTACION
            else{
                $entrada = $c[$i];
                if($this->esLetra($entrada))           $entrada = 0; // Letra
                elseif($this->esNumerico($entrada))    $entrada = 1; // Digito
                else return false;
            }
            $estado = $this->_afd[$estado][$entrada];
            if($estado === false || $estado === true) return $estado;
            $i++;
        }
    }

    function printTokens(){
        $ii=0;
        echo "RESULTADO DEL ANALIZADOR";
        echo "
        <table class='lexer'>
            <thead>
                <tr>
                    <td bgcolor=#9f9f9f style=color:blue>NRO</td>
                    <td bgcolor=#9f9f9f style=color:blue>LEXEMA</td>
                    <td bgcolor=#9f9f9f style=color:blue>TIPO</td>
                    <td bgcolor=#9f9f9f style=color:blue>LINEA</td>
                </tr>
            </thead>
            <tbody>";

        foreach ($this->_tokens as $num => $item) {
            echo "<tr><td>".($num+1)."</td>";
            foreach ($item as $valor){
                if($valor == "ERROR"){
                     $valor = "<b style=color:red>".$valor."</b>";
                    $ii=$ii+1;}
                echo "<td>".$valor."</td>";
            }
                echo "</tr>";
        }
        echo "</tbody>
          </table>";
         echo "<table><tr><td>";
          echo "se verifico <b style=color:red>".$valor."</b> lineas de codigo<br>";
          echo "</td></tr><tr><td>";
          echo "se encontro <b style=color:red>".$ii."</b> error";
          echo "</td></tr>";
          echo "</table>";
    }

    
}