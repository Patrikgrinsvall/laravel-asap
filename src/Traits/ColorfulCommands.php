<?php
declare(strict_types=1);
namespace SilentRidge\Asap\Traits;

/**
 * Trait related to colorful output in terminal. 
 * (c) Part of SilentRidge boilerplate package
 *
 * by <patrik@silentridge.io>
 */
trait ColorfulCommands {
 
    private ?\ArrayObject $theme = null;

    /**
     * Set COLORTERM=true as environment variable in order to have true color support
     */
    public function __construct(
        public $primaryFG = "white",
        public $primaryBG = "black",

        public $secondaryFG = "black",
        public $secondaryBG = "white",   

        public $accentFG = "yellow",
        public $accentBG = "black") 
    {}
    
    function initializeTheme() 
    {
        $this->primaryFG = "white";
        $this->primaryBG = "black";

        $this->secondaryFG = "black";
        $this->secondaryBG = "white";   

        $this->accentFG = "yellow";
        $this->accentBG = "black"; 
        $this->theme = new \ArrayObject(array(), \ArrayObject::STD_PROP_LIST);
        $this->theme->primaryFG     = $this->primaryFG;
        $this->theme->primaryBG     = $this->primaryBG;
        $this->theme->secondaryFG   = $this->secondaryFG;
        $this->theme->secondaryBG   = $this->secondaryBG;
        $this->theme->accentFG      = $this->accentFG;
        $this->theme->accentBG      = $this->accentBG;
    }

    /**
     * Will print each sub element in its own color
     *
     * @param array $strings
     * @return void
     */
    function printInfo(...$strings) : void
    {
        if(!$this->theme) $this->initializeTheme();
        $line = "<fg=" . $this->theme->accentBG . ";bg=" . 
            $this->theme->accentFG . ">[<fg=" . $this->theme->accentFG . ";bg=" . 
            $this->theme->accentBG . ";options=bold>" . 
            " " . date("Y-m-d h:i:s") . " " .
            "</>]</> ";

        foreach($strings as $string) {

            if (!is_string($string))
                continue;

            $string = trim($string);
            if(empty($string)) continue;
            $col = $this->randomDarkWebColor();
            $bg = $this->randomLightWebColor();//$this->isColorBright($col['rgb']['r'], $col['rgb']['g'], $col['rgb']['b']) ? "bg=black" : "bg=white";
            $line .= sprintf("<fg=#%s;%s> %s </>", $col['hex'], $bg['hex'], $string);
        }
        // $line . "</><fg=magenta;bg=black>[ <fg=white;bg=black;options=bold> </> ] </>";
        $this->line($line);
    }

  /**
     * Will print each sub element in its own color
     *
     * @param array $strings
     * @return void
     */
    function printRGB(...$strings) : void
    {
        $line = "<fg=magenta;bg=black>[ <fg=white;bg=black;options=bold>" . date("Y-m-d h:i:s") . "</> ] </>";
        $length = 0;
        foreach($strings as $string) {

            if (!is_string($string))
                continue;

            $string = trim($string);
            if(empty($string)) continue;
            $col = $this->randomWebColor();
            $bg = $this->isColorBright($col['rgb']['r'], $col['rgb']['g'], $col['rgb']['b']) ? "bg=black" : "bg=white";
            $line .= sprintf("<fg=#%s;%s> %s </>", $col['hex'], $bg, $string);
            $length += strlen($string);
        }
        $line . "</><fg=magenta;bg=black>[ <fg=white;bg=black;options=bold> </> ] </>";
        $this->line($line);
    }

    /**
     * Returns true if the supplied RGB color is bright, false if dark.
     */
    public function isColorBright(int $r,int $g,int $b) : Bool
    {
        $hsp = sqrt(
            0.299 * ($r * $r) +
            0.587 * ($g * $g) +
            0.114 * ($b * $b)
        );

        if ($hsp > 127.5)
            return true;
        else
            return false;
    }

    /**
     * 
     */
    function randomDarkWebColor() : Array 
    {
        do {
            $color = $this->randomWebColor();
        } while(true === $this->isColorBright(
                $color['rgb']['r'],
                $color['rgb']['g'],
                $color['rgb']['b']
            )
        );
        return $color;
    }
    function randomLightWebColor() : Array 
    {
        do {
            $color = $this->randomWebColor();
        } while(false === $this->isColorBright(
                $color['rgb']['r'],
                $color['rgb']['g'],
                $color['rgb']['b']
            )
        );
        return $color;
    }
    /**
     * Returns a random hex color and a corresponding array with RGB values
     *
     * @return array
     */
    function randomWebColor() : Array
    {
        $hex = "";
        $rgb = [];
        foreach(array('r', 'g', 'b') as $color)
        {
            $val = mt_rand(0, 255);
            $dechex = dechex($val);
            if(strlen($dechex) < 2) {
                $dechex = "0" . $dechex;
            }
            $hex .= strtoupper($dechex);
            $rgb[$color] = $val;
        }

        return [
            'hex' => $hex,
            'rgb' => $rgb
        ];
    }
}
