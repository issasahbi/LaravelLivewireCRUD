<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Rule;
use App\Models\Todo;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:3|max:50')]
    public $name;

    public $EditingTodoID;

    #[Rule('required|min:3|max:50')]
    public $EditingNewName;

    public $search;

    public function toggle($id)
    {
        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }
    public function edit($id)
    {
        $this->EditingTodoID = $id;
        $this->EditingNewName = Todo::find($id)->name;
    }
    public function cancelEdit()
    {
        $this->reset('EditingTodoID', 'EditingNewName');
    }
    public function update()
    {
        $this->validateOnly('EditingNewName');
        Todo::find($this->EditingTodoID)->update(
            [
                'name' => $this->EditingNewName
            ]
        );
        $this->cancelEdit();
        @session()->flash('update', 'todo has updated');
    }
    public function create()
    {
        //validate
        $validated = $this->validateOnly('name');
        //create tod
        Todo::create($validated);
        // clear the input 
        $this->reset('name');
        //send flash message
        session()->flash('success', 'todo created');
        $this->resetPage();
    }
    public function deleteTodo($id)
    {
        try {
            // $todo->delete();
            Todo::findOrfail($id)->delete();
        } catch (\Exception $ex) {
            session()->flash('error', 'Failed to delete this todo');
        }
    }
    public function render()
    {

        return view('livewire.todo-list', [
            'todos' => Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(5)
        ]);
    }
}
