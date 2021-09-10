<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use App\Imports\PortfolioImport;
use App\Http\Requests\PortfolioRequest;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.portfolios.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.portfolios.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PortfolioRequest $request)
    {
        $portfolio = auth()->user()->portfolios()->create($request->all(), ['owner' => true]);

        return redirect(route('portfolio.show', $portfolio->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function show(Portfolio $portfolio)
    {
        $this->authorize('view', $portfolio);

        return view('pages.portfolios.show', ['portfolio' => $portfolio]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function edit(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        return view('pages.portfolios.edit', ['portfolio' => $portfolio]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function update(PortfolioRequest $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $portfolio->update($request->validated());

        // make sure we don't remove owner
        $users = array_merge($request->input('users'), [$portfolio->owner_id]);

        $portfolio->users()->sync($users);

        return redirect(route('portfolio.show', $portfolio->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Portfolio  $portfolio
     * @return \Illuminate\Http\Response
     */
    public function destroy($portfolio)
    {
        $this->authorize('delete', $portfolio);

        $portfolio->delete();

        return redirect(route('portfolio.index'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $file = $request->file('import')->store('/', 'local');
        
        $import = (new PortfolioImport)->import($file, 'local', \Maatwebsite\Excel\Excel::XLSX);

        return redirect(route('portfolio.index')); 
    }
}
