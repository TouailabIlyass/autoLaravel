<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Achat;

class AchatController extends Controller
{
    public function index()
    {
        $achats = Achat::all();

        return view('achats.index', compact('achats'));
    }

    public function create()
    {
        return view('achats.create');
    }

    public function store()
    {
        $achats = Achat::create($this->validateData());

        return redirect('/achats/'.$achats->id);
    }

    //Route Model Binding => \App\Customer $var
    public function show(Achat $achat)
    {
        return view('achats.show', compact('achat'));
    }

    public function edit(Achat $achat)
    {
        return view('achats.edit', compact('achat'));
    }

    public function update(Achat $achat)
    {
        $achat->update($this->validateData());    

        return redirect('/achats/'.$achats->id);
    }

    public function destroy(Achat $achat)
    {
        $achat->delete();

        return redirect('/achats');
    }


    public function validateData()
            {
            	return request()->validate([
				'id_achat'=> 'required',
				'id_client'=> 'required',
				'id_user'=> 'required',
				'total'=> 'required',
				'date_achat'=> 'required',
			]);
		}


    //----------------------------------------Rest Controllers----------------------
    
    public function restIndex($limit = 0)
    {
        return Achat::limit(99)->offset($limit)->get();
    }

    public function restStore()
    {
        return Achat::create($this->validateData());
    }

    //Route Model Binding => \App\Customer $var
    public function restShow(Achat $achat)
    {
        return $achat;
    }

    
    public function restUpdate(Achat $achat)
    {
        return $achat->update($this->validateData());
    }

    public function RestDestroy(Achat $achat)
    {
        return $achat->delete();
    }
}
        