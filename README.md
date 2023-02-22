# Create pdfs with reusable templates

## What does this package do
Create pdfs by creating reusable templates. Optionally you can define different base styles to share between your pdf's. Think in term of logo's, colors, spacing, etc. 

A pdf can be built using clear syntax with variable and extendable options for every item you create. This package is built for Laravel but can also be used with vanilla PHP or any other framework. This too is interchangable. 


## How to create a document

```php
class ExamplePdf
{
    private function build() : Document
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
                }, new RowSettings);
            }, new TableSettings);
        })->break(false)->lineHeight(1.5);

        $document->title('Some title')->download(); // or inline or whatever
    }
    
    // these can all be moved to traits if needed
    private function title() : Title
    {
        return (new Title($section))
            ->text('Title')
            ->font('Calibri')
            ->margin(2, 4) // like css; top and bottom have 2, left right have 4
            ->padding(2); // Like css: All sides 2 points padding
            // etc;
    }

    private function paragraph($text) : Paragraph
    {
        return (new Paragraph($section))
            ->text($text);
            // etc;
    }
}
```