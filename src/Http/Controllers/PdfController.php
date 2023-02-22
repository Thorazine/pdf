<?php

namespace Thorazine\Pdf\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuoteController extends Controller
{
    public function index()
    {
        // start a document and load the settings that are default for all
        // These settings overwrite the default settings. 
        // therefor settings are optional as there is a default
        $document = new Document(new SomeSettings());

        // We do not add pages. Pages are added automatically. Instead we add sections and create pages as we go and as fits
        $document->addSection(function($section) {
            // section passes back to document but has the document in it

            // add a titleor a paragraph
            $section->add($this->title());
            $section->add($this->paragraph('Some text'));

            // tables are more complex. Once in a table you can add rows and cells
            $section->addTable(function($table) {

                // add a row
                $table->addRow(function($row) {

                    // each cell in the row might have a different height
                    // but we take the highest and equalize the rest
                    // to fit it. This makes advanced tables impossible
                    // but it standardizes the table
                    $row->addCell(function($cell) {

                        // lets drop a paragraph in the cell
                        $cell->add($this->paragraph('Cell 1'));
                    })->width(60);
                    $row->addCell(function($cell) {

                        // lets drop a title in the cell
                        $cell->add($this->title('Some title'));
                    })->width(40);

                    // so this row has two cells. By default we will be 
                    // assuming that you'd like to have two columns
                    // each with 50% width. If you want to have a different
                    // width you can set it here as we did in percentages with 
                    // the function width(). But the function grid() is also
                    // available. This will set the width in grid units.
                    // mix and match as you like
                });
            });
        })->break(false)->lineHeight(1.5);
    }
    
    // these can all be moved to traits if needed
    private function title()
    {
        return (new Title($section))
            ->text('Title')
            ->font('Calibri')
            ->mt(2)
            ->mb(2);
            // etc;
    }

    private function paragraph($text)
    {
        return (new Paragraph($section))
            ->text($text);
            // etc;
    }
}