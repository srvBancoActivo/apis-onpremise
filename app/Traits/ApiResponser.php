<?php

namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    /**
     * Retorna una respuesta satisfactoria de elementos en formato json.
     *
     * @param $data
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse($data, $code)
    {
        //return response()->json($data, $code);
        return $data;
    }

    /**
     * Retorna mensaje de error.
     *
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code)
    { //echo "el co " . $code;
        //return response()->json(['error' => $message], $code);
    return response()->json($message, $code);
    }

    /**
     * Retorna una lista o coleccion de elementos.
     *
     * @param Collection $collection
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function showAll(Collection $collection, $code = 200)
    {

        if ($collection->isEmpty())
        {
            return $this->successResponse(['data' => $collection],$code);
        }

        //$transformer = $collection->first()->transformer;

        //$collection = $this->filterData($collection, $transformer);

        //$collection = $this->sortData($collection, $transformer);

        //$collection = $this->paginate($collection);

        //$collection = $this->transformData($collection, $transformer);

        //$collection = $this->cacheResponse($collection);

        return $this->successResponse($collection,$code);
    }

    /**
     * Retorna una instancia de un modelo.
     *
     * @param Model $instace
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function showOne(Model $instace, $code = 200)
    {
        $transformer = $instace->transformer;

        $instace     = $this->transformData($instace, $transformer);

        return $this->successResponse($instace,$code);
    }

    /**
     * Retorna un mensaje.
     *
     * @param $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message],$code);
    }

    /**
     * Filtrado de data de coleccion.
     *
     * @param Collection $collection
     * @param $transformer
     * @return Collection
     */
    protected function filterData(Collection $collection, $transformer)
    {
        /* Con query obtenemos toda la lista de parametros de la peticion*/

        foreach (request()->query() as $query => $value)
        {
            $attribute  = $transformer::originalAttribute($query);

            if (isset($attribute, $value))
            {
                $collection = $collection->where($attribute, $value);
            }
        }

        return $collection;
    }

    /**
     * Ordena una colleccion.
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function sortData(Collection $collection, $transformer)
    {
        if(request()->has('sort_by'))
        {
            $attribute  = $transformer::originalAttribute(request()->sort_by);

            $collection = $collection->sortBy->{$attribute};
        }

        return $collection;
    }

    /**
     * Retorna un coleccion paginada.
     *
     * @param Collection $collection
     * @return LengthAwarePaginator
     */
    protected function paginate(Collection $collection)
    {
        $rules = [

            'per_page' => 'integer|min:2|max:50'
        ];

        Validator::validate(request()->all(),$rules);

        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 15;

        if(request()->has('per_page'))
        {
            $perPage = (int) request()->per_page;
        }

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page,[

            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;
    }

    /**
     * Transforma la data recibida por parametro.
     *
     * @param $data
     * @param $transformer
     * @return array
     */
    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);

        return $transformation->toArray();
    }

    /**
     * Funcion que agrega a la caché la data pasada por parametro.
     * Esto es para no recargar tanto la base de datos.
     *
     * @param $data
     * @return mixed
     */
    protected function cacheResponse($data)
    {
        $url = request()->url();
        $queryParams = request()->query();

        //Ordenamos los parametros
        ksort($queryParams);

        //construimos el query string
        $queryString = http_build_query($queryParams);

        // construimos la url completa
        $fullUrl = "{$url}?{$queryString}";

        return Cache::remember($fullUrl, 30/60, function() use($data){

            return $data;
        });
    }
}
