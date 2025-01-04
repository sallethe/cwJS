<?php

class Nothing extends Component
{


    public function getCode(): string
    {

        return '
        <div class="GridButton">
            <img src="/cwJS/_public/resources/icons/error.svg" alt="Nothing">
            <h1>Rien</h1>
            <h2>???</h2>
        </div>
        ';
    }
}