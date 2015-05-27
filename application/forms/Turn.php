<?php

/**
 * Allows insertion of scores for a game turn.
 */
class DartsGame_Form_Turn extends Zend_Form
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setName('turn');

        $firstScore = $this->buildScoreInput('scores0');
        $firstMultiplier = $this->buildMultiplierSelect('multipliers0');

        $secondScore = $this->buildScoreInput('scores1');
        $secondMultiplier = $this->buildMultiplierSelect('multipliers1');

        $thirdScore = $this->buildScoreInput('scores2');
        $thirdMultiplier = $this->buildMultiplierSelect('multipliers2');

        $submit = new Zend_Form_Element_Submit('submit');

        $this->addElements(
            array(
                $firstScore,
                $firstMultiplier,
                $secondScore,
                $secondMultiplier,
                $thirdScore,
                $thirdMultiplier,
                $submit
            )
        );
    }

    /**
     * Returns a new score input.
     *
     * @param string $name
     * @return Zend_Form_Element_Text
     */
    protected function buildScoreInput($name)
    {
        $firstScore = new Zend_Form_Element_Text($name);

        return $firstScore->setLabel('Points')
            ->setBelongsTo('scores')
            ->setRequired(true)
            ->addFilter('Int');
    }

    /**
     * Returns a new multiplier select.
     *
     * @param string $name
     * @return Zend_Form_Element_Select
     */
    protected function buildMultiplierSelect($name)
    {
        $select = new Zend_Form_Element_Select($name);

        return $select->setLabel('Multiplier')
            ->setBelongsTo('multipliers')
            ->addMultiOptions(array(
                    '1' => 'x 1',
                    '2' => 'x 2',
                    '3' => 'x 3'
                )
            );
    }
}
